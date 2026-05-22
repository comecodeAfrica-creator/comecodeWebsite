<?php

/**
 * Database Configuration
 * Update these values with your actual MySQL credentials
 */

// Database connection details
define('DB_HOST', 'localhost');           // MySQL host
define('DB_USER', 'root');                // MySQL username
define('DB_PASS', '');                    // MySQL password (empty for local)
define('DB_NAME', 'comecode_db');         // Database name
define('DB_CHARSET', 'utf8mb4');

/**
 * Database Connection
 * Establish and test MySQL connection
 */
function getDBConnection(): ?mysqli
{
    static $conn = null;

    if ($conn === null) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        // Check connection
        if ($conn->connect_error) {
            error_log('Database Connection Error: ' . $conn->connect_error);
            return null;
        }

        // Set charset
        $conn->set_charset(DB_CHARSET);
    }

    return $conn;
}

/**
 * Close Database Connection
 */
function closeDBConnection(): void
{
    global $conn;
    if (isset($conn) && $conn) {
        $conn->close();
    }
}

/**
 * Execute a prepared statement with parameters
 * @param string $query SQL query with placeholders
 * @param array $params Parameters to bind
 * @param string $types Parameter types (e.g., 'sss' for three strings)
 * @return mysqli_result|bool Result or false on error
 */
function executeQuery(string $query, array $params = [], string $types = ''): mysqli_result|bool|null
{
    $conn = getDBConnection();
    if (!$conn) {
        return null;
    }

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        error_log('Prepare Error: ' . $conn->error);
        return false;
    }

    if (!empty($params) && !empty($types)) {
        if (!$stmt->bind_param($types, ...$params)) {
            error_log('Bind Error: ' . $stmt->error);
            return false;
        }
    }

    if (!$stmt->execute()) {
        error_log('Execute Error: ' . $stmt->error);
        return false;
    }

    return $stmt->get_result();
}

/**
 * Get single row from query
 */
function getOne(string $query, array $params = [], string $types = ''): ?array
{
    $result = executeQuery($query, $params, $types);
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return null;
}

/**
 * Get all rows from query
 */
function getAll(string $query, array $params = [], string $types = ''): array
{
    $result = executeQuery($query, $params, $types);
    $rows = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
    }
    return $rows;
}

/**
 * Insert record and get last insert ID
 */
function insertRecord(string $table, array $data): int|false
{
    $conn = getDBConnection();
    if (!$conn) {
        return false;
    }

    $columns = array_keys($data);
    $placeholders = array_fill(0, count($data), '?');

    $query = "INSERT INTO $table (" . implode(',', $columns) . ") VALUES (" . implode(',', $placeholders) . ")";

    $types = str_repeat('s', count($data));
    $params = array_values($data);

    $result = executeQuery($query, $params, $types);
    if ($result !== false) {
        return $conn->insert_id;
    }

    return false;
}

/**
 * Update record
 */
function updateRecord(string $table, array $data, string $whereClause, array $whereParams = []): bool
{
    $conn = getDBConnection();
    if (!$conn) {
        return false;
    }

    $setClause = implode(',', array_map(fn($k) => "$k=?", array_keys($data)));
    $query = "UPDATE $table SET $setClause WHERE $whereClause";

    $params = array_values($data) + $whereParams;
    $types = str_repeat('s', count($params));

    return executeQuery($query, $params, $types) !== false;
}

/**
 * Delete record
 */
function deleteRecord(string $table, string $whereClause, array $whereParams = []): bool
{
    return executeQuery("DELETE FROM $table WHERE $whereClause", $whereParams, str_repeat('s', count($whereParams))) !== false;
}

/**
 * Get row count
 */
function countRecords(string $table, string $whereClause = '', array $whereParams = []): int
{
    $query = "SELECT COUNT(*) as count FROM $table";
    if (!empty($whereClause)) {
        $query .= " WHERE $whereClause";
    }
    $result = getOne($query, $whereParams, str_repeat('s', count($whereParams)));
    return $result['count'] ?? 0;
}

/**
 * Test database connection
 */
function testDBConnection(): bool
{
    $conn = getDBConnection();
    return $conn !== null && $conn->ping();
}
