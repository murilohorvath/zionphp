<?php
namespace zion\orm;

use DateTime;
use Exception;
use PDO;
use PDOException;
use zion\core\System;

/**
 * @author Vinicius Cesar Dias
 * Classe para criar uma camada entre o banco de dados (MySQL,DB2 etc) e a aplicação PHP
 * Design Pattern: DAO (Data Access Object)
 */
abstract class AbstractDAO {
    public static $metadataCache  = array();
    public static $translateError = true;
    
    protected $DBMS;
	protected $tableName;
	protected $metadata;
	
	/**
	 * Inicializa carregando os metadados da tabela. Os metadados são obrigatórios
	 * caso queira efetuar operações de persistência
	 * @param $db
	 * @param string $tableName
	 * @throws Exception
	 */
	public function __construct($db = null, $tableName = ""){
		$this->tableName = $tableName;
		if($db != null AND $tableName != ""){
		    $this->metadata = $this->loadMetadata($db,$tableName);
		    if(sizeof($this->metadata) <= 0){
		        throw new Exception("Metadados não encontrado para a tabela ".$tableName);
		    }
		}
	}
	
	/**
	 * Carrega os metadados da tabela
	 */
	abstract public function loadMetadata(PDO $db,string $tableName) : array;
	
	/**
	 * Retorna os metadados da entidade
	 * @return array
	 */
	public function getMetadata() : array {
	    return $this->metadata??[];
	}
	
	/**
	 * Retorna o próximo id disponível
	 * @param PDO $db
	 * @param string $name
	 * @return int
	 */
	abstract public function getNextId(PDO $db, string $name) : int;
	
	/**
	 * Lança uma exceção personalizada
	 */
	public function throwException(Exception $e,array $errorInfo){
	    throw $e;
	}
	
	/**
	 * Retorna um array associativo com informações da chave primária do objeto. 
	 * É um array pois a chave pode ser composta
	 * @return array
	 */
	public function getPKs() : array {
		$output = array();
		foreach($this->metadata AS $fieldName => $md){
			if($md->isPK){
				$output[$fieldName] = $md;
			}
		}
		return $output;
	}
	
	/**
	 * Adiciona delimitadores em uma palavra reservada, evitando erros na execução de comandos
	 * @param string $reservedWord
	 * @return string
	 */
	abstract public function addDelimiters(string $reservedWord) : string;
	
	/**
	 * Converte um array contendo a chave primaria em uma CLAUSULA WHERE
	 * @param array $keys
	 * @return string
	 */
	public function parseKeys(array $keys): string {
	    $dbConfig = System::get("db-config");
	    
	    if (sizeof($keys) <= 0) {
	        throw new Exception("WHERE vazio");
	    }
	    
	    $tmp = array();
	    
	    foreach ($keys AS $key => $value) {
	        if ($value === null) {
	            $tmp[] = $this->addDelimiters($key)." IS NULL";
	            continue;
	        }
	        
	        if ($key == "") {
	            throw new Exception("Chave vazia");
	        }
	        
	        $md = $this->metadata[$key];
	        
	        switch ($md->nativeType) {
	            case "integer":
	                $tmp[] = $this->addDelimiters($key)." = ".intval($value);
	                break;
	            case "double":
	                $tmp[] = $this->addDelimiters($key)." = ".floatval($value);
	                break;
	            case "date":
	                if ($value instanceof \DateTime){
	                    $tmp[] = $this->addDelimiters($key)." = '".$value->format($dbConfig["dateFormat"])."'";
	                }
	                break;
	            case "datetime":
	                if ($value instanceof \DateTime){
	                    $tmp[] = $this->addDelimiters($key)." = '".$value->format($dbConfig["dateTimeFormat"])."'";
	                }
	                break;
	            default:
	                $tmp[] = $this->addDelimiters($key)." = '".addslashes($value)."'";
	                break;
	        }
	    }
	    
	    return " WHERE ".implode(" AND ",$tmp);
	}
	
	/**
	 * Converte um filtro em clausulas SQL, começando do WHERE
	 * @param $filter
	 * @return string
	 */
	abstract public function parseFilter(Filter $filter = null) : string;
	
	/**
	 * Converte qualquer tipo de filtro em clausula sql
	 * @param $arg1
	 * @throws PDOException
	 * @return string
	 */
	public function parseAnyFilter($filter) : string {
	    if($filter === null){
	       return "";
	    }
	    
	    if(is_array($filter) AND sizeof($filter) > 0){
	       return $this->parseKeysWhere($filter);
	    }
	    
	    if($filter instanceof Filter){
	        return $this->parseFilter($filter);
	    }
	    
	    throw new PDOException("Filtro não suportado!");
	}
	
	/**
	 * Interpreta uma lista de campos e converte em uma string para a clausula SELECT
	 * @param array $fields
	 * @return string
	 */
	public function parseFields(array $fields = array()) : string {
	    if (sizeof($fields) <= 0) {
	        return "*";
	    }
	    
	    $output = array();
	    foreach ($fields AS $field) {
	        $output[] = $this->addDelimiters($field);
	    }
	    return implode(", ",$output);
	}
	
	/**
	 * Retorna um array contendo a chave primária do objeto
	 * @param $obj
	 * @return array
	 */
	public function getKeysByObject(ObjectVO $obj) : array {
	    $keys = array();
	    foreach ($this->metadata AS $fieldName => $md) {
	        if ($md->isPK) {
	            $keys[$fieldName] = $obj->get($fieldName);
	        }
	    }
	    return $keys;
	}
	
	/**
	 * Executa um SQL nativo no banco de dados e retorna um array com o resultado já em objetos
	 * @param $db
	 * @param string $sql
	 * @param Filter $filter
	 * @param string $outputType
	 * @return array
	 */
	public function queryAndFetch(PDO $db,string $sql, $filter = null, $outputType="object") : array {
	    $sql .= $this->parseAnyFilter($filter);
	    
	    System::set("lastSQL",$sql);
	    
	    $pdo_stmt = $db->prepare($sql);
	    $pdo_stmt->execute();
	    
	    $output = array();
	    while($row = $pdo_stmt->fetch(PDO::FETCH_NUM)){
	        $row2 = array();
	        foreach($row as $column_index => $column_value){
	            $metaf      = $pdo_stmt->getColumnMeta($column_index);
	            $fieldName  = $metaf["name"];
	            $fieldValue = null;
	            $fieldType  = strtolower($metaf["native_type"]);
	            
	            switch($fieldType){
                case "double":
                case "float":
                case "decimal":
                case "real":
                    $fieldValue = doubleval($column_value);
                    break;
                case "tinyint":
                case "smallint":
                case "int":
                case "integer":
                case "long":
                case "bigint":
                    $fieldValue = intval($column_value);
                    break;
                case "bool":
                case "boolean":
                    $fieldValue = ($column_value === true);
                    break;
                case "tinyblob":
                case "smallblob":
                case "blob":
                case "longblob":
                case "bigblob":
                case "binary":
                    $fieldValue = ($column_value === true);
                    break;
                case 'date':
                case 'datetime':
                case 'timestamp':
                    $fieldValue = null;
                    
                    // tentativa 1
                    if($column_value != ""){
                        try {
                            $fieldValue = new DateTime($column_value);
                        }catch(Exception $e){}
                    }
                    
                    // tentativa 2
                    if(!($fieldValue instanceof DateTime)){
                        $parts = explode(" ",$column_value);
                        $month = $parts[0];
                        $day = $parts[1];
                        $year = $parts[2];
                        $timefull = $parts[3];
                        
                        $temp = explode(":",$timefull);
                        $time = $temp[0].":".$temp[1].":".$temp[2];
                        $subtype = strtoupper($temp[3]);
                        if($subtype == "PM"){
                            // somar 12 horas
                        }
                        
                        $final = $year."-".$month."-".$day." ".$time;
                        try {
                            $fieldValue = new DateTime(date("Y-m-d H:i:s",strtotime($final)));
                        }catch(Exception $e){}
                    }
                    
                    // tentativa 3
                    if(!($fieldValue instanceof DateTime)){
                        $fieldValue = $column_value;
                    }
                    break;
                default:
                    $fieldValue = $column_value;
                    break;
	            }
	            
	            $row2[$fieldName] = $fieldValue;
	        }
	        
	        if($outputType == "array"){
	            $output[] = $row2;
	        }else{
	            $obj = new ObjectVO();
	            $obj->setAll($row2);
	            $output[] = $obj;
	        }
	    }
	    
	    return $output;
	}
	
	/**
	 * Retorna um array de objetos
	 * @param $db
	 * @param $filter
	 * @param array $fields
	 * @return array
	 */
	public function getArray(PDO $db,$filter=null,array $fields=array()) : array {
	    $sql = "SELECT ".$this->parseFields($fields)."
				  FROM ".$this->addDelimiters($this->tableName);
	    $sql .= $this->parseAnyFilter($filter);
	    return $this->queryAndFetch($db, $sql);
	}
	
	/**
	 * Retorna um objeto
	 * @param $db
	 * @param $keys
	 * @param array $fields
	 * @return ObjectVO|null
	 */
	public function getObject(PDO $db,array $keys, array $fields=array()){
	    $TOP = "";
	    if($this->DBMS == "MSSQL"){
	        $TOP = " TOP 1";
	    }
	    
	    $sql = "SELECT".$TOP." ".$this->parseFields($fields)."
				  FROM ".$this->addDelimiters($this->tableName);
	    $sql .= $this->parseKeys($keys);
	    
	    if($this->DBMS != "MSSQL"){
	        $sql .= " LIMIT 1";
	    }
	    
	    $result = $this->queryAndFetch($db, $sql);
	    if(sizeof($result) == 1){
	        return $result[0];
	    }
	    
	    return null;
	}
	
	/**
	 * Verifica se um objeto existe no banco de dados
	 * @param PDO $db
	 * @param $objOrKeys
	 * @throws Exception
	 * @return bool
	 */
	public function exists(PDO $db, $objOrKeys) : bool {
	    $keys = array();
	    
	    if (is_array($objOrKeys)) {
	        $keys = $objOrKeys;
	    } else {
	        $keys = $this->getKeysByObject($objOrKeys);
	    }
	    
	    if(sizeof($keys) <= 0){
	        throw new Exception("exists(): Nenhuma chave informada para verificar se o objeto existe");
	    }
	    
	    return ($this->getObject($db,$keys) != null);
	}
	
	/**
	 * Conta quantos objetos tem na tabela com base no filtro
	 * @param $db
	 * @param $filter
	 * @return int
	 */
	public function count(PDO $db,$filter) : int {
	    $sql = "SELECT count(*) AS total
				  FROM ".$this->addDelimiters($this->tableName);
	    
	    $sql .= $this->parseAnyFilter($filter);
	    $query = $this->query($sql);
	    
	    $result = 0;
	    if($raw = $query->fetch(PDO::FETCH_ASSOC)){
	        $result = $raw["total"];
	    }
	    return $result;
	}
	
	/**
	 * Executa uma consulta no banco de dados com um tratamento especial de erros
	 * @param PDO $db
	 * @param string $sql
	 * @throws PDOException
	 * @return
	 */
	public function query(PDO $db,string $sql){
	    $query = false;
	    try {
	        $query = $db->query($sql);
	    } catch (PDOException $e) {
	        $this->throwException($e,$db->errorInfo());
	    }
	    
	    if ($query === false) {
	        $einfo = $db->errorInfo();
	        throw new PDOException("DAO::query() Erro na consulta: [".$einfo[0]." | ".$einfo[1]."] ".$einfo[2]);
	    }
	    
	    return $query;
	}
	
	/**
	 * Executa uma atualização no banco de dados
	 * @param $db
	 * @param string $sql
	 * @return int
	 */
	public function exec(PDO $db, string $sql) : int {
	    try {
	       return $db->exec($sql);
	    } catch (PDOException $e) {
	        $this->throwException($e,$db->errorInfo());
	    }
	}
	
	/**
	 * Insere um objeto no banco de dados
	 * @param $db
	 * @param $obj
	 * @param array $options
	 * @return string
	 */
	public function insert(PDO $db,ObjectVO &$obj,array $options=array()) : int {
	    $dbConfig = System::get("db-config");
	    
	    // ignora erros
	    $IGNORE = "";
	    if ($options["ignoreErrors"]){
	        $IGNORE = " IGNORE";
	    }
	    
	    $sql = "INSERT".$IGNORE." INTO ".$this->addDelimiters($this->tableName)." ";
	    
	    $fields1 = array();
	    $fields2 = array();
	    
	    foreach ($this->metadata AS $fieldName => $md) {
	        // se o campo não foi informado, ignora
	        if(!$obj->has($fieldName)){
	            continue;
	        }
	        
	        $fields1[] = $this->addDelimiters($fieldName);
	        
	        if($obj->get($fieldName) === null){
	            $fields2[] = "null";
	        }else{
	            switch ($md->nativeType) {
                case "date":
                    $fields2[] = "'".$obj->get($fieldName)->format($dbConfig["dateFormat"])."'";
                    break;
                case "datetime":
                    $fields2[] = "'".$obj->get($fieldName)->format($dbConfig["dateTimeFormat"])."'";
                    break;
                case "integer":
                    $fields2[] = intval($obj->get($fieldName));
                    break;
                case "double":
                    $fields2[] = floatval($obj->get($fieldName));
                    break;
                case "blob":
                    //$fields2[] = "'".addslashes($obj->get($fieldName))."'";
                    $fields2[] = "UNHEX('".bin2hex($obj->get($fieldName))."')";
                    break;
                default:
                    $fields2[] = "'".addslashes($obj->get($fieldName))."'";
                    break;
	            }
	        }
	    }
	    
	    $sql .= "(".implode(", ",$fields1).")";
	    $sql .= " VALUES ";
	    $sql .= "(".implode(", ",$fields2).")";
	    
	    try {
	        $result = $db->exec($sql);
	    } catch (PDOException $e) {
	        $this->throwException($e,$db->errorInfo());
	    }
	    
	    return $result;
	}
	
	/**
	 * Atualiza um objeto no banco de dados
	 */
	public function update(PDO $db,ObjectVO $obj,$filter=null) : int {
	    $dbConfig = System::get("db-config");
	    
	    $sql = "UPDATE ".$this->addDelimiters($this->tableName);
	    
	    // separando campos que são chaves dos que não são chaves
	    $bufferPK0 = array();
	    $bufferPK1 = array();
	    $allFields = $obj->getAll();
	    
	    foreach ($allFields AS $fieldName => $fieldValue) {
	        // ignorando campos que não existem na tabela
	        if (!array_key_exists($fieldName,$this->metadata)) {
	            continue;
	        }
	        
	        // metadados do campo
	        $md = $this->metadata[$fieldName];
	        
	        if ($fieldValue === null) {
	            $line = $this->addDelimiters($fieldName)." = null";
	        } else {
	            $line = "";
	            
	            switch ($md->nativeType) {
	                case "date":
	                    if ($fieldValue instanceof \DateTime) {
	                        $line = $this->addDelimiters($fieldName)." = '".$fieldValue->format($dbConfig["dateFormat"])."'";
	                    } else {
	                        $line = $this->addDelimiters($fieldName)." = null";
	                    }
	                    break;
	                case "datetime":
	                    if ($fieldValue instanceof \DateTime){
	                        $line = $this->addDelimiters($fieldName)." = '".$fieldValue->format($dbConfig["dateTimeFormat"])."'";
	                    } else {
	                        $line = $this->addDelimiters($fieldName)." = null";
	                    }
	                    break;
	                case "integer":
	                    $line = $this->addDelimiters($fieldName)." = ".intval($fieldValue);
	                    break;
	                case "double":
	                    $line = $this->addDelimiters($fieldName)." = ".doubleval($fieldValue);
	                    break;
	                default:
	                    $line = $this->addDelimiters($fieldName)." = '".addslashes($fieldValue)."'";
	                    break;
	            }
	        }
	        
	        if ($md->isPK) {
	            $bufferPK1[] = $line;
	        } else {
	            $bufferPK0[] = $line;
	        }
	    }
	    
	    // se não há campos a serem atualizados, não faz nada
	    if (sizeof($bufferPK0) <= 0) {
	        return 0;
	    }
	    
	    // se informar um filtro, atualizando todos os campos
	    if ($filter instanceof Filter) {
	        $bufferAll = array_merge($bufferPK0,$bufferPK1);
	        $sql .= " SET ".implode(", ",$bufferAll);
	        $sql .= $this->parseFilter($filter);
	    } else {
	        // campos atualizáveis
	        $sql .= " SET ".implode(", ",$bufferPK0);
	        
	        // campos chaves
	        $sql .= " WHERE ".implode(" AND ",$bufferPK1);
	    }
	    return $this->exec($db, $sql);
	}
	
	/**
	 * Insere ou atualiza um objeto no banco de dados
	 * @param $db
	 * @param $obj
	 * @return int
	 */
	public function insertOrUpdate(PDO $db,ObjectVO &$obj) : int {
	    if ($this->exists($db,$obj)) {
	        return $this->update($db, $obj);
	    } else {
	        return $this->insert($db, $obj);
	    }
	}
	
	/**
	 * Alias
	 * @param $db
	 * @param ObjectVO $obj
	 * @return int
	 */
	public function replace(PDO $db,ObjectVO &$obj) : int {
	    return $this->insertOrUpdate($db, $obj);
	}
	
	/**
	 * Atualiza um campo na tabela
	 * @param $db
	 * @param $field
	 * @param $value
	 * @param Filter $filter
	 * @throws Exception
	 * @return int
	 */
	public function updateField(PDO $db,string $field,$value,$filter=null) : int {
	    $sql = "UPDATE ".$this->addDelimiters($this->tableName);
	    if($value === null){
	        $sql .= "SET ".$this->addDelimiters($field)." = null";
	    }else{
	        $sql .= "SET ".$this->addDelimiters($field)." = '".addslashes($value)."'";
	    }
	    $WHERE = $this->parseAnyFilter($filter);
	    if($WHERE == ""){
	        throw new Exception("DAO->updateField() Sem condições para atualizar");
	    }
	    $sql .= $WHERE;
	    
	    return $this->exec($db, $sql);
	}
	
	/**
	 * Incrementa um campo
	 * @param $db
	 * @param string $field
	 * @param array $keys
	 * @param int $quantity
	 * @return int
	 */
	abstract public function increase(PDO $db,string $field,array $keys,int $quantity=1) : int;
	
	/**
	 * Decrementa um campo
	 * @param $db
	 * @param string $field
	 * @param array $keys
	 * @param int $quantity
	 * @return int
	 */
	abstract public function decrease(PDO $db,string $field,array $keys,int $quantity=1) : int;
	
	/**
	 * Remove um ou mais registros
	 * @param $db
	 * @param $arg1
	 * @throws Exception
	 * @return string
	 */
	public function delete(PDO $db,$arg1) : int {
	    $sql = "DELETE FROM ".$this->addDelimiters($this->tableName);
	    $sql .= $this->parseAnyFilter($arg1);
	    if($sql == ""){
	        throw new Exception("DELETE sem filtro informado");
	    }
	    return $this->exec($db,$sql);
	}
}
?>