<?php
// Server information
$server   = "LAPTOP-A48SL481\NODE1PRIMARY";
$database = "mahasiswa";
$uid      = ""; 
$pwd      = "";

// Connection
try {
    $conn = new PDO("sqlsrv:server=$server;Database=$database", $uid, $pwd);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch( PDOException $e ) {
    die( "Error connecting to SQL Server".$e->getMessage());
}

// Statement
$sql = "
    DECLARE @date VARCHAR(19)
    SET @date = CONVERT(VARCHAR(19), GETDATE(), 126)
    SET @date = REPLACE(@date, ':', '-')
    SET @date = REPLACE(@date, 'T', '-')
    
    DECLARE @fileName VARCHAR(100)
    SET @fileName = ('C:\log\BackUp_' + @date + '.bak')
    
    BACKUP DATABASE mahasiswa
    TO DISK = @fileName
    WITH 
        FORMAT,
        STATS = 1, 
        MEDIANAME = 'SQLServerBackups',
        NAME = 'Full Backup of dbname';
";
try {
    $stmt = $conn->prepare($sql);
    $stmt->execute();
} catch (PDOException $e) {
    die ("Error executing query. ".$e->getMessage());
}

// Clear buffer
try {
    while ($stmt->nextRowset() != null){};
    echo "Success";
} catch (PDOException $e) {
    die ("Error executing query. ".$e->getMessage());
}

// End
$stmt = null;
$conn = null;
?>