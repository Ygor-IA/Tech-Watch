
$db = new SQLite3("tech-watch");
$res = $db->query("SELECT count(*) as count FROM users");
$row = $res->fetchArray();
echo "Users: " . $row["count"] . "\n";
$res2 = $db->query("SELECT count(*) as count FROM componentes_hardware");
if ($res2) {
    $row2 = $res2->fetchArray();
    echo "Componentes: " . $row2["count"] . "\n";
} else {
    echo "Table componentes_hardware not found.\n";
}

