<?php
/**
 * Copyright 2013-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2013-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Mongo
 */

namespace Horde\Mongo;

use MongoClient;
use MongoCollection;
use Serializable;

/**
 * Extend the base PECL MongoClient class by allowing it to be serialized.
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @category  Horde
 * @copyright 2013-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Mongo
 */
class Client extends MongoClient implements Serializable
{
    /**
     * Database name (Horde\Mongo\Client uses this single database by default
     * to ease configuration).
     *
     * @var string
     */
    public string $dbname = 'horde';

    /**
     * Constructor args.
     *
     * @var array
     */
    private array $_cArgs;

    /**
     * @see MongoClient#__construct
     */
    public function __construct($server = null, array $options = [])
    {
        $this->_cArgs = [$server, $options];
        parent::__construct($server, $options);
    }

    /* Database name is hardcoded into Horde\Mongo\Client. */

    /**
     * @deprecated
     * @see MongoClient#dropDB
     */
    public function dropDB($db)
    {
        if (empty($db)) {
            $db = $this->dbname;
        }
        return parent::dropDB($db);
    }

    /**
     * @see MongoClient#selectCollection
     */
    public function selectCollection($db, $collection = null)
    {
        if (empty($db)) {
            $db = $this->dbname;
        }
        return parent::selectCollection($db, $collection);
    }

    /**
     * @see MongoClient#selectDB
     */
    public function selectDB($name)
    {
        if (!empty($name)) {
            $this->dbname = $name;
        }

        return parent::selectDB($this->dbname);
    }

    /* Horde\Mongo\Client specific methods. */

    /**
     * Checks that indices are up-to-date.
     *
     * @param mixed $collection  The collection name or a MongoCollection
     *                           object.
     * @param array $indices     The index definition (see ensureIndex()).
     *
     * @return boolean  True if the indices are up-to-date.
     */
    public function checkIndices($collection, array $indices)
    {
        $coll = ($collection instanceof MongoCollection)
            ? $collection
            : $this->selectCollection(null, $collection);
        $info = $coll->getIndexInfo();

        foreach ($indices as $key => $val) {
            foreach ($info as $val2) {
                if (($val2['name'] == $key) && ($val2['key'] == $val)) {
                    continue 2;
                }
            }
            return false;
        }

        return true;
    }

    /**
     * Create indices for the collection.
     *
     * @param mixed $collection  The collection name or a MongoCollection
     *                           object.
     * @param array $indices     The index definition (see ensureIndex()).
     */
    public function createIndices($collection, array $indices)
    {
        $coll = ($collection instanceof MongoCollection)
            ? $collection
            : $this->selectCollection(null, $collection);
        $coll->deleteIndexes();

        foreach ($indices as $key => $val) {
            $coll->ensureIndex($val, [
                'background' => true,
                'name' => $key,
                'w' => 0,
            ]);
        }
    }

    /* Serializable methods. */

    /**
     */
    public function serialize()
    {
        $this->close();
        return serialize([
            $this->dbname,
            $this->_cArgs,
        ]);
    }

    /**
     */
    public function unserialize($data)
    {
        [$this->dbname, $this->_cArgs] = unserialize($data);
        parent::__construct($this->_cArgs[0], $this->_cArgs[1]);
    }
}
