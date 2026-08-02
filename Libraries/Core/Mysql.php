<?php
    class Mysql extends Conexion{
        private $conexion;
        private $queries;
        private $values;
        function __construct(){
            $this->conexion = new Conexion();
            $this->conexion = $this->conexion->conect();
        }
        public function insert(string $query, array $worth){
            $this->queries = $query;
            $this->values = $worth;
            $insert = $this->conexion->prepare($this->queries);
            $response = $insert->execute($this->values);
            if($response){
                $lastInsert = $this->conexion->lastInsertId();
            }else{
                $lastInsert = 0;
            }
            return $lastInsert;
        }
        public function select(string $query, ?array $worth = null){
            $this->queries = $query;
            $this->values = $worth;
            $result = $this->conexion->prepare($this->queries);
            $result->execute($this->values);
            $return = $result->fetch(PDO::FETCH_ASSOC);
            return $return;
        }
        public function run_simple_query($query, ?array $worth = null){
           $this->queries = $query;
           $this->values = $worth;
           $result = $this->conexion->prepare($this->queries);
           $result->execute($this->values);
           return $result;
        }
        public function select_all(string $query, ?array $worth = null){
            $this->queries = $query;
            $this->values = $worth;
            $result = $this->conexion->prepare($this->queries);
            $result->execute($this->values);
            $return = $result->fetchall(PDO::FETCH_ASSOC);
            return $return;
        }
        public function update(string $query, array $worth){
            $this->queries = $query;
            $this->values = $worth;
            $update = $this->conexion->prepare($this->queries);
            $return = $update->execute($this->values);
            return $return;
        }
        public function delete(string $query, ?array $worth = null){
            $this->queries = $query;
            $this->values = $worth;
            $result = $this->conexion->prepare($this->queries);
            $result->execute($this->values);
            return $result;
        }
        
        /* ============================================================
         * TRANSACCIONES PDO
         * ============================================================ */
        
        /**
         * Inicia una transacción
         */
        public function beginTransaction(): bool {
            return $this->conexion->beginTransaction();
        }
        
        /**
         * Confirma la transacción
         */
        public function commit(): bool {
            return $this->conexion->commit();
        }
        
        /**
         * Revierte la transacción
         */
        public function rollback(): bool {
            return $this->conexion->rollBack();
        }
        
        /**
         * Verifica si hay una transacción activa
         */
        public function inTransaction(): bool {
            return $this->conexion->inTransaction();
        }
        
        /**
         * Obtiene la conexión PDO directamente
         * Útil para operaciones avanzadas
         */
        public function getConnection(): PDO {
            return $this->conexion;
        }
    }
