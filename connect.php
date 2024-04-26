<?php

        //CST d'environnement
            define("DBhost","localhost");
            define("DBuser","root");
            define("DBpass","siosql");
            define("DBname","sharerecipe");

            //DSN de connection
            $dsn="mysql:dbname=".DBname.";host=".DBhost;
            
            //on va se connecter à la base
            try{
                //on instancie PDO
                $db= new PDO($dsn, DBuser, DBpass);
                // echo "connexion réussie";

                //on s'assure d'envoyer les données en utf8
                $db->exec("set names 'utf8'");
                
                //on def le mode de fetch par défaut
                 $db-> setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC);
            }
            catch(PDOException $e){
                die($e->getMessage());
            }
            //on est connecté à la base
?>