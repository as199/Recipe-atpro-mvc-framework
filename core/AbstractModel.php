<?php

namespace Atpro\mvc\core;

use PDO;
use PDOStatement;

abstract class AbstractModel extends Database
{
    protected $table;
    protected const RULE_REQUIRED = 'required';
    protected const RULE_EMAIL = 'email';
    protected const RULE_MIN = 'min';
    protected const RULE_MAX = 'max';
    protected const RULE_MATCH = 'match';
    protected const RULE_UNIQUE = 'unique';

    private array $errors = [];


    /**
     * @return array|false egale PDO::FETCH_ASSOC =>2, PDO::FETCH_OBJ=>5,PDO::FETCH_DEFAULT=>0,
     * egale PDO::FETCH_ASSOC =>2, PDO::FETCH_OBJ=>5,PDO::FETCH_DEFAULT=>0,
     * PDO::FETCH_COLUMN =>7
     * Permet de recuperer toutes les lignes d'une table
     */
    public function findAll()
    {
        $result =  $this->req("SELECT * FROM  $this->table");
        $result->setFetchMode(PDO::FETCH_CLASS, get_class($this));
        return $result->fetchAll();
    }

    /**
     * @param array $criteres
     * @return array|false
     * Permet de recuperer des lignes via des criteres
     */
    public function findBy(array $criteres)
    {
        $champs = [];
        $valeurs = [];
        foreach ($criteres as $champ => $valeur) {
            $champs[] = "$champ = ?";
            $valeurs[] = $valeur;
        }
        $liste_champs = implode(' AND ', $champs);
        $result= $this->req("SELECT * FROM  $this->table  WHERE  $liste_champs", $valeurs);
        $result->setFetchMode(PDO::FETCH_CLASS, get_class($this));
        return $result->fetchAll();
    }

    /**
     * @param array $criteres
     * @return array|false
     * Permet de recuperer une colonne via un array de critéres
     */
    public function findOneBy(array $criteres)
    {
        $champs = [];
        $valeurs = [];
        foreach ($criteres as $champ => $valeur) {
            $champs[] = "$champ = ?";
            $valeurs[] = $valeur;
        }
        $liste_champs = implode(' AND ', $champs);
        $result =  $this->req("SELECT * FROM  $this->table  WHERE  $liste_champs", $valeurs);
        $result->setFetchMode(PDO::FETCH_CLASS, get_class($this));
        return  $result->fetchAll();
    }

    /**
     * @param string $login
     * @param string $password
     * @return bool
     * Permet de se connecter en donne array
     */
    public function auth(string $login, string $password): bool
    {
        $db = $this->getInstance();
        $result =  $db->prepare("SELECT * FROM  $this->table  WHERE email=:email");
         $result->execute(['email'=>$login]);
        $data =$result->fetch(PDO::FETCH_ASSOC);
        if (!empty($data) && password_verify($password, $data['password'])) {
            setSession($data);
            return true;
        }
        return false;
    }

    /**
     * @param string $login
     * @param string $password
     * Permet de se connecter en donne array
     * @return mixed|void
     */
    public function authApi(string $login, string $password)
    {
        $result =  $this->req("SELECT * FROM  $this->table  WHERE email=?", [$login]);
        $result->setFetchMode(PDO::FETCH_CLASS, get_class($this));
        $data = $result->fetch();
        if (!empty($data) && password_verify($password, $data->password)) {
            return $data;
        }
        return null;
    }

    /**
     * @param int $id
     * @return array|false
     * Permet de recuperer une colonne via son id
     */
    public function find(int $id)
    {
        $result =  $this->req("SELECT * FROM $this->table WHERE id=$id");
        $result->setFetchMode(PDO::FETCH_CLASS, get_class($this));
        return $result->fetchAll();
    }

    /**
     * @param array $except les champs as ne pas inserer
     * @return false|PDOStatement
     * Permet d'inserer un objet dans la base de donne
     */
    public function create(array $except = [])
    {
        $champs = [];
        $inter = [];
        $valeurs = [];
        foreach ($this as $champ => $valeur) {
            $setter = 'set' . ucfirst($champ);
            if (method_exists($this, $setter) && !in_array($champ, $except, true)) {
                $champs[] = $champ;
                $inter[] = "?";
                if ($setter === 'setPassword') {
                    $valeurs[] = password_hash($valeur, PASSWORD_ARGON2I);
                } else {
                    $valeurs[] = $valeur;
                }
            }
        }
        $liste_champs = implode(', ', $champs);
        $liste_inter = implode(', ', $inter);
        return $this->req("INSERT INTO  $this->table ($liste_champs)VALUES($liste_inter)", $valeurs);
    }

    /**
     * @param $data
     * @return int
     * Permet d'inserer dans la base de donne
     */
    public function save($data): int
    {
        $champs = [];
        $inter = [];
        $valeurs = [];
        foreach ($data as $champ => $valeur) {
            $champs[] = $champ;
            $inter[] = "?";
            $valeurs[] = $valeur;
        }
        $liste_champs = implode(', ', $champs);
        $liste_inter = implode(', ', $inter);
        $rep= $this->req("INSERT INTO  $this->table ($liste_champs)VALUES($liste_inter)", $valeurs);
        return $rep->rowCount()=== 1;
    }

    /**
     * @return bool|false
     * Permet de mettre à jour une ligne
     */
    public function update(): bool
    {
        $champs = [];
        $valeurs = [];
        foreach ($this as $champ => $valeur) {
            $setter = 'set' . ucfirst($champ);
            if (method_exists($this, $setter)) {
                $champs[] = "$champ = ?";
                $valeurs[] = $valeur;
            }
        }
        $valeurs[] = $this->id;
        $liste_champs = implode(', ', $champs);
        $result= $this->req("UPDATE  $this->table  SET  $liste_champs  WHERE id = ?", $valeurs);
        return $result->rowCount()=== 1;
    }


    /**
     * @param int $id
     * @return int
     * Permet de supprimer un element par son id
     */
    public function delete(int $id): int
    {
        $rep= $this->req("DELETE FROM $this->table WHERE id = ?", [$id]);
        return $rep->rowCount()===1;
    }

    /**
     * @return int
     * Permet de calculer le nombre de lignes
     */
    public function count(): int
    {
        $stmt = $this->req(" SELECT * FROM $this->table");
        return $stmt->rowCount();
    }
    /**
     * @param string $sql
     * @param array|null $attributs
     * @return false|PDOStatement
     */
    public function req(string $sql, array $attributs = null)
    {
        $db = $this->getInstance();
        if ($attributs !== null) {
            $query = $db->prepare($sql);
            $query->execute($attributs);
            return $query;
        }
        return $db->query($sql);
    }

    /**
     * @param array $donnees
     * @return $this
     * Creer un objet a partir d'un array
     */
    public function hydrate(array $donnees): self
    {
        foreach ($donnees as $key => $value) {
            $setter = 'set' . ucfirst($key);
            if (method_exists($this, $setter)) {
                $this->$setter($value);
            }
        }
        return $this;
    }

    /**
     * @param array $donnees
     * @return array
     * Creer un objet a partir d'un array
     */
    public function hydrateToArray(array $donnees): array
    {
        foreach ($donnees as $key => $value) {
            $setter = 'set' . ucfirst($key);
            if (method_exists($this, $setter)) {
                $data[$key]=$value;
            }
        }
        return $data;
    }

    /**
     * Permet de verifier si une propriété appartient à la classe
     * @param $data
     */
    public function loadData($data): void
    {
        foreach ($data as $key => $value) {
            $setter = 'set' . ucfirst($key);
            if (method_exists($this, $setter)) {
                $this->{$key} = $value;
            }
        }
    }

    abstract public function rules(): array;

    /**
     * Permet de valider les different champs d'un formulaire
     * @return bool
     */
    public function validate(): bool
    {

        foreach ($this->rules() as $attribute => $rules) {
            $value = $this->{$attribute};
            foreach ($rules as $rule) {
                $ruleName = $rule;
                if (!is_string($ruleName)) {
                    $ruleName = $rule[0];
                }
                if ($ruleName  === self::RULE_REQUIRED && !$value) {
                    $this->addErrorForRule($attribute, self::RULE_REQUIRED);
                }
                if ($ruleName  === self::RULE_EMAIL && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addErrorForRule($attribute, self::RULE_EMAIL);
                }
                if ($ruleName === self::RULE_MIN && strlen($value) < $rule['min']) {
                    $this->addErrorForRule($attribute, self::RULE_MIN, $rule);
                }
                if ($ruleName === self::RULE_MAX && strlen($value) > $rule['max']) {
                    $this->addErrorForRule($attribute, self::RULE_MAX, $rule);
                }
                if ($ruleName === self::RULE_MATCH && $value !== $this->{$rule['match']}) {
                    $this->addErrorForRule($attribute, self::RULE_MATCH, $rule);
                }
                if ($ruleName === self::RULE_UNIQUE) {
                    $uniqueAttribute= $attribute = $rule['attribute'] ?? $attribute;
                    $tableName = $this->table;
                    $statement= $this->getInstance()
                        ->prepare("SELECT * FROM $tableName WHERE $uniqueAttribute =:attribute");
                    $statement->bindValue(":attribute", $value);
                    $statement->execute();
                    $record = $statement->fetchObject();
                    if ($record) {
                        $this->addErrorForRule($attribute, self::RULE_UNIQUE, ['field'=> $value]);
                    }
                }
            }
        }
        return (empty($this->errors));
    }

    /**
     * Permet d'ajouter les messages d'erreur
     * @param $attribute
     * @param $rule
     * @param array $params
     */
    private function addErrorForRule($attribute, $rule, array $params = []): void
    {
        $message = $this->errorMessages()[$rule] ?? '';
        foreach ($params as $key => $value) {
            $message = str_replace("$key", $value, $message);
        }
        $this->errors[$attribute][] = $message;
    }

    /**
     * Permet d'ajouter les messages d'erreur
     * @param $attribute
     * @param $message
     */
    public function addError($attribute, $message): void
    {
        $this->errors[$attribute][] = $message;
    }

    /**
     * Retour les different erreurs suivants les cas possibles
     * @return string[]
     */
    public function errorMessages(): array
    {
        return [
            self::RULE_REQUIRED => 'This field is required',
            self::RULE_EMAIL => 'This field must be valid email address',
            self::RULE_MIN => 'Min length of this field must be {min}',
            self::RULE_MAX => 'Max length of this field must be {max}',
            self::RULE_MATCH => 'This field must be same as {match}',
            self::RULE_UNIQUE => 'Record with this {field} already exists'
        ];
    }

    /**
     * Permet de voir si les champs est valide ou pas
     * @param $attribute
     * @return false|mixed
     */
    public function hasError($attribute)
    {
        return $this->errors[$attribute] ?? false;
    }

    /**
     * Permet de recuper le message d'eurreur
     * @param $attribute
     * @return false|mixed
     */
    public function getFirstError($attribute)
    {
        return $this->errors[$attribute][0] ?? false;
    }

    /**
     * Retourne les eurreurs de validation
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
