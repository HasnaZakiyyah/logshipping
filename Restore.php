<?php
// Server information
$server   = "LAPTOP-A48SL481\NODE2SECONDARY";
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
CREATE TABLE myFileList (FileNumber INT IDENTITY, FileName VARCHAR(256))

DECLARE @Path varchar(256) = 'dir C:\Log\'
DECLARE @Command varchar(1024) = @Path + ' /A-D /B'
INSERT INTO myFileList
EXEC MASTER.dbo.xp_cmdshell @Command

declare @databasename varchar(100)

select @databasename = (select TOP 1 FileName
from myFileList order by cast (  (SUBSTRING(filename,8,4))+'/'+(SUBSTRING(filename,13,2))+'/'+(SUBSTRING(filename,16,2))+' '+
(SUBSTRING(filename,19,2))+':'+(SUBSTRING(filename,22,2))+':'+(SUBSTRING(filename,25,2)) as datetime  )

desc)

print @databasename
declare @query varchar(max);
set @query='RESTORE DATABASE [mahasiswa]
FROM disk=''C:\Log\'+@databasename+''' WITH REPLACE'
print @query
execute (@query)

DROP TABLE myFileList
GO

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