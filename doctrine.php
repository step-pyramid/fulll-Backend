<?php

require_once __DIR__. '/bootstrap.php';

use Doctrine\ORM\Tools\SchemaTool;

// Assuming $entityManager is already created in bootstrap.php
$schemaTool = new SchemaTool($entityManager);

// Get all entity metadata
$metadata = $entityManager->getMetadataFactory()->getAllMetadata();

// Function to drop the schema
function dropSchema() {
    global $schemaTool, $metadata;
    try {
        $schemaTool->dropSchema($metadata);
        echo "Database schema dropped successfully!\n";
    } catch (\Exception $e) {
        echo "Failed to drop database schema: ". $e->getMessage(). "\n";
    }
}

// Function to update the schema
function updateSchema() {
    global $schemaTool, $metadata;
    try {
        $schemaTool->updateSchema($metadata);
        echo "Database schema updated successfully!\n";
    } catch (\Exception $e) {
        echo "Failed to update database schema: ". $e->getMessage(). "\n";
    }
}

// Function to create the schema
function createSchema() {
    global $schemaTool, $metadata;
    try {
        $schemaTool->createSchema($metadata);
        echo "Database schema created successfully!\n";
    } catch (\Exception $e) {
        echo "Failed to create database schema: ". $e->getMessage(). "\n";
    }
}

// Execute the command based on the argument
if ($argc > 1 && $argv[1] === 'drop') {
    dropSchema();
} elseif ($argc > 1 && $argv[1] === 'update') {
    updateSchema();
} elseif ($argc > 1 && $argv[1] === 'create') {
    createSchema();
} else {
    echo "Usage: php doctrine.php <command>\n";
    echo "Commands: drop, update, create\n";
}
