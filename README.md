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
        'user'    => array(
            'class'           => 'Symflo\MongoDBODM\Document\UserDocument',
            'collectionName'  => 'users',
            'collectionClass' => 'Symflo\MongoDBODM\Document\UserCollection'
        ),
        'preference' => array(
            'class' => 'Symflo\MongoDBODM\Document\PreferenceDocument'
        ),
        'message'    => array(
            'class'           => 'Symflo\MongoDBODM\Document\MessageDocument',
            'collectionName'  => 'messages'
        ),
        'role'    => array(
            'class' => 'Symflo\MongoDBODM\Document\RoleDocument'
        )
    ),
    'types' => array(
        //'custom' => new \CustomPath\Type\myDateType(),
    )
);

//Prefer DIC as this factory 
list($indexer, $dm) = DocumentManagerFactory::create($config);

//Multiple insert
$message = new \Symflo\MongoDBODM\Document\MessageDocument();
$message->setId((string) new \MongoId());
$message->setText('Text1');

$message2 = new \Symflo\MongoDBODM\Document\MessageDocument();
$message2->setId((string) new \MongoId());
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

Manage simple embedded.

```php
<?php
$preference = new \Symflo\MongoDBODM\Document\PreferenceDocument();
$preference->setAlert(true);

$user = new \Symflo\MongoDBODM\Document\UserDocument();
$user->setUsername('TES3');
$user->setFirstname('Norris');
$user->setMessage($message);
$user->setMessages(array($message, $message2));
$user->setCreatedAt(new \MongoDate());
$user->setRoles(new DocumentCollection(array($roleAdmin, $roleAdmin, $roleSuperAdmin))); //Collection in user
$user->setPreference($preference); //or $user->setPreference(array('alert' => true));
$dm->save($user);

//then

$coll = $dm->getCollection('users');
$coll->addJoin('preference') // get object instead of array
     ->findOneByUsername('TES3');

?>
```

Manage embedded collection.

```php
<?php
$roleAdmin = new \Symflo\MongoDBODM\Document\RoleDocument();
$roleAdmin->setRole('ROLE_ADMIN');
$roleAdmin->setAddedAt((new DateTime())->format('Y-m-d H:i:s'));

$roleSuperAdmin = new \Symflo\MongoDBODM\Document\RoleDocument();
$roleSuperAdmin->setRole('ROLE_SUPER_ADMIN');
$roleSuperAdmin->setAddedAt((new DateTime())->format('Y-m-d H:i:s'));

$user = new \Symflo\MongoDBODM\Document\UserDocument();
//$user->setId('your_custom_id');
$user->setUsername('TES3');
$user->setFirstname('Norris');
$user->setMessage($message);
$user->setMessages(array($message, $message2));
$user->setCreatedAt(new \MongoDate());
$user->setRoles(new DocumentCollection(array($roleAdmin, $roleAdmin, $roleSuperAdmin))); //Collection in user
$dm->save($user);

$roleRoot = new \Symflo\MongoDBODM\Document\RoleDocument();
$roleRoot->setRole('ROLE_ROOT');
$roleRoot->setAddedAt((new DateTime())->format('Y-m-d H:i:s'));

//push end roles collection
$dm->push($user, 'roles', $roleRoot);

?>
```

## Installation

Use Composer to install this library.

Into your composer.json file, just include this library with adding:

"symflo/mongodb-odm": "dev-master"
Then, run composer update symflo/mongodb-odm and enjoy.


## Prepare your config
```php
<?php
$config = array(
    'user'                => 'user',
    'password'            => 'password',
    'database'            => 'db',
    'host'                => '127.0.0.1',
    'documents'           => array(
        'user'    => array(
            'class'           => 'Symflo\MongoDBODM\Document\UserDocument',
            'collectionName'  => 'users',
            'collectionClass' => 'Symflo\MongoDBODM\Document\UserCollection'
        ),
        'message'    => array(
            'class'           => 'Symflo\MongoDBODM\Document\MessageDocument',
            'collectionName'  => 'messages'
        )
        // ...
    ),
    'types' => array(
        //'custom' => new \CustomPath\Type\myDateType(), if you want create a custom type with own validator
        // see Symflo\MongoDBODM\DocumentManagerFactory::create() for list
    )
);
?>
```

## Create yours documents

### User

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

    private $username;
    private $firstname;
    private $lastname;
    private $isAdmin = false; //default value
    private $createdAt;
    private $messageId;
    private $messageIds;
    private $message;
    private $messages;
    private $roles;
    private $preference;

    /**
     * {% inheritdoc %}
     */
    public function getProperties()
    {
        return array(
            'username'   => array('type' => 'string', 'required' => true),
            'firstname'  => array('type' => 'string', 'required' => true),
            'lastname'   => array('type' => 'string'),
            'isAdmin'    => array('type' => 'boolean'),
            'createdAt'  => array('type' => 'date'),
            'messageId'  => array('type' => 'manualReference', 'reference' => 'message', 'target' => 'message'),
            'messageIds' => array('type' => 'manualReferences', 'reference' => 'message', 'target' => 'messages'),
            'roles'      => array('type' => 'embeddedCollection', 'reference' => 'role'),
            'preference' => array('type' => 'embeddedOne', 'reference' => 'preference') //'reference' => 'preference' if you want a document else 'reference' => false to avoid to create a document class and get an array
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

    public function getIsAdmin()
    {
        return $this->isAdmin;
    }
    
    public function setIsAdmin($isAdmin)
    {
        $this->isAdmin = $isAdmin;
    }

    public function getPreference()
    {
        return $this->preference;
    }
    
    public function setPreference($preference)
    {
        $this->preference = $preference;
    }
}
?>
```

### Preference

If you indicate a reference for `embeddedOne` type, you have to create a document.

```php
<?php
class PreferenceDocument implements DocumentInterface
{
    private $alert;

    public function getProperties()
    {
        return array(
            'alert'  => array('type' => 'boolean')
        );
    }

    public function getMongoId()
    {
        return false;
    }

    public function getAlert()
    {
        return $this->alert;
    }
    
    public function setAlert($alert)
    {
        $this->alert = $alert;
    }
}
?>
```

### Message

```php
<?php
class MessageDocument implements DocumentInterface
{
    use \Symflo\MongoDBODM\Behaviour\MongoIdTrait;

    private $text;

    /**
     * {% inheritdoc %}
     */
    public function getProperties()
    {
        return array(
            'id'    => array('type' => 'string', 'required' => true), //only if you want to define a other type else you get \MongoId
            'text'  => array('type' => 'string', 'required' => true)
        );
    }

    public function getText()
    {
        return $this->text;
    }
    
    public function setText($text)
    {
        $this->text = $text;
    }
}
?>
```

### Role

```php
<?php

namespace Symflo\MongoDBODM\Document;

class RoleDocument implements DocumentInterface
{
    private $role;
    private $addedAt;

    public function getProperties()
    {
        return array(
            'role'    => array('type' => 'string', 'required' => true),
            'addedAt' => array('type' => 'string', 'required' => true)
        );
    }

    public function getMongoId()
    {
        return false;
    }

    public function getRole()
    {
        return $this->role;
    }
    
    public function setRole($role)
    {
        $this->role = $role;
    }

    public function getAddedAt()
    {
        return $this->addedAt;
    }
    
    public function setAddedAt($addedAt)
    {
        $this->addedAt = $addedAt;
    }
}
?>
```

If you want create your document collection.

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
```

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
    public function validate($value)
    {
        //your own logic
    }

    public function getError()
    {
        // return string
    }

    public function hydrate($value, $propertyOptions)
    {
        return $value;
    }
}
?>
```
Then add your class in your config.

## Listeners

Listeners list:
- preSave
- postSave
- preCreate
- postCreate
- preUpdate
- postUpdate
- preRemove
- postRemove

Example on `UserDocument`

```php
<?php

class UserDocument implements DocumentInterface
{
    // ...
    public function preSave()
    {
        if ($this->getFirstname() == 'Norris') {
            $this->setFirstname('Norris_test');
        }
    }

    // ...
?>
```

## Errors

```php
<?php

if (!$dm->save($user)) {
    foreach ($dm->getValidatorErrors() as $error) {
        echo $error['property']; //property name
        echo $error['document']; //class document
        echo $error['message']; //message
    }
}

?>
```

## EnsureIndex

Add on your collection static method `indexes`.
```php
<?php
class UserCollection extends Collection
{
    // ...

    public static function getIndexes()
    {
        return array(
            array('keys' => array('createdAt' => 1), 'options' => array('expireAfterSeconds' => 60))
            //...
        );
    }

    // ...
}
?>
```
Note:[expireAfterSeconds](http://www.codeproject.com/Tips/467689/MongoDB-Time-To-Live-TTL-Collections).

Then apply indexes for example during a task. When you apply it, the old indexes are deleted and new ones are created.

```php
<?php
list($indexer, $dm) = DocumentManagerFactory::create($config);
$indexer->applyIndex();
?>
```

## TODOS
* transform method `getProperties` in config
* service Listeners for preSave... more complex
* Better errors management
* Tests
* Better docs
