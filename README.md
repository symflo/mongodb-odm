# MongoDB ODM for PHP

Simple Object Document Mapper for PHP and MongoDB.

## Requirements

* PHP 5.4+
* MongoDB driver

## Basic Usage

```php
<?php

require 'vendor/autoload.php';

use \Symflo\MongoDBODM\DocumentManagerFactory;

$config = array(
    'user'                => 'user',
    'password'            => 'password',
    'database'            => 'db',
    'host'                => '127.0.0.1',
    'documents'           => array(
        'user'    => 'Symflo\MongoDBODM\Document\UserDocument',
        'message' => 'Symflo\MongoDBODM\Document\MessageDocument'
    ),
    'types' => array(
        //'custom' => new \CustomPath\Type\myDateType(),
    )
);

//Prefer DIC as this factory 
$dm = DocumentManagerFactory::create($config);

//Multiple insert
$message = new \Symflo\MongoDBODM\Document\MessageDocument();
$message->setText('Text1');

$message2 = new \Symflo\MongoDBODM\Document\MessageDocument();
$message2->setText('Text2');

$dm->batchInsert(array($message, $message2));
?>
```
Simple Insert, update and delete.

```php
<?php
$user = new \Symflo\MongoDBODM\Document\UserDocument();
$user->setUsername('CN');
$user->setFirstname('Chuck');
$user->setMessage($message); //manual reference
$user->setMessages(array($message, $message2)); //manual references
$user->setCreatedAt((new DateTime())->format('Y-m-d H:i:s'));
//insert
$dm->save($user);

//update
$user->setFirstname('Chucky')
$dm->save($user);

//delete
$dm->remove($user);
?>
```

To find objects, use native query from `\MongoCollection`.

```php
<?php
$coll = $dm->getCollection('users');
$users = $coll->find(); //return DocumentCollection
$user = $coll->findOne(array('username' => 'CN')); //return Document
$user->getUsername(); //return 'CN'
$user->getMongoId(); //return \MongoId

$coll->drop();
?>
```
Create query with your object collection.

Without joins:
```php
<?php
$coll = $dm->getCollection('users');
$user = $coll->findOneByUsername('CN');
$user->getMessages(); //return null
$user->getMessageIds(); //return \MongoId array
$user->getMessage(); //return null
$user->getMessageId(); //return \MongoId
?>
```

With joins:

```php
<?php
$coll->addJoin('messageId')
     ->addJoin('messageIds')
     ->findOneByUsername('TES3');
$user->getMessages(); //return DocumentCollection MessageDocument
$user->getMessageIds(); //return \MongoId array
$user->getMessage(); //return MessageDocument
$user->getMessageId(); //return \MongoId

?>
```

## Installation


## Prepare your config
```php
<?php
$config = array(
    'user'                => 'user',
    'password'            => 'password',
    'database'            => 'db',
    'host'                => '127.0.0.1',
    'documents'           => array(
        'user'    => 'Your\NS\Document\UserDocument',
        'message' => 'Your\NS\Document\MessageDocument',
        ...
    ),
    'types' => array(
        //'custom' => new \CustomPath\Type\myDateType(), if you want create a custom type with own validator
        // see Symflo\MongoDBODM\Configurator::getDefaultConfig() for list
    )
);
?>
```


## Create your document

```php
<?php

namespace Your\NS\Document;

use Symflo\MongoDBODM\Document\CollectionHandler;
use Symflo\MongoDBODM\Document\DocumentInterface;

/**
 * UserDocument
 */
class UserDocument implements DocumentInterface
{
    use \Symflo\MongoDBODM\Behaviour\MongoIdTrait;

    const COLLECTION_NAME   = 'users'; //
    const COLLECTION_OBJECT = 'Symflo\MongoDBODM\Document\UserCollection'; //if you precice an object to manage collection else put null

    private $username;
    private $firstname;
    private $lastname;
    private $createdAt;
    private $messageId;
    private $message;
    private $messages;

    /**
     * {% inheritdoc %}
     */
    public function getProperties()
    {
        return array(
            'username'   => array('type' => 'string', 'required' => true),
            'firstname'  => array('type' => 'string', 'required' => true),
            'lastname'   => array('type' => 'string'),
            'createdAt'  => array('type' => 'date'),
            'messageId'  => array('type' => 'manualReference', 'reference' => 'Symflo\MongoDBODM\Document\MessageDocument', 'target' => 'message'),
            'messageIds' => array('type' => 'manualReferences', 'reference' => 'Symflo\MongoDBODM\Document\MessageDocument', 'target' => 'messages'),
        );
    }

    public function getMessages()
    {
        return $this->messages;
    }
    
    public function setMessages($messages)
    {
        $this->messages = $messages;
        $this->setMessageIds(CollectionHandler::getCollectionIds($messages));
    }

    public function getMessageIds()
    {
        return $this->messageIds;
    }
    
    public function setMessageIds($messageIds)
    {
        $this->messageIds = $messageIds;
    }

    public function getMessage()
    {
        return $this->message;
    }
    
    public function setMessage($message)
    {
        $this->setMessageId(CollectionHandler::getId($message));
        $this->message = $message;
    }

    public function getMessageId()
    {
        return $this->messageId;
    }
    
    public function setMessageId($messageId)
    {
        $this->messageId = $messageId;
    }

    public function getUsername()
    {
        return $this->username;
    }
    
    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getFirstname()
    {
        return $this->firstname;
    }

    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    public function getLastname()
    {
        return $this->lastname;
    }
    
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }
}
?>
```

Create if you want your document collection.

```php
<?php
namespace Symflo\MongoDBODM\Document;

use Symflo\MongoDBODM\Document\Collection;

/**
 * UserCollection
 */
class UserCollection extends Collection
{
    public function findByUsername($username)
    {
        return $this->getCollectionHandler()->find(array('username' => $username));
    }

    public function findOneByUsername($username)
    {
        return $this->getCollectionHandler()->findOne(array('username' => $username));
    }
}
?>
``

## Create your own type
```php
<?php 
namespace Your\NS\Type;

use Symflo\MongoDBODM\Type\TypeInterface;

/**
 * YourType
 */
class YourType implements TypeInterface
{
    /**
     * {% inheritdoc %}
     */
    public function validate($value)
    {
        //own logic
    }

    /**
     * {% inheritdoc %}
     */
    public function getError()
    {
        // return string
    }
}
?>
```
Then add your class in your config.

## TODOS
* Better errors management with exceptions
* Tests
* Better docs
