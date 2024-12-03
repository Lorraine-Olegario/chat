<?php

require_once('./../vendor/autoload.php');

try {
    $databasePath = 'database.sqlite';
    $pdo = new PDO("sqlite:" . $databasePath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

    // Valores a serem usados na chave composta
    $userIdToDelete = 1;
    $conversationIdToDelete = 2;

    // SQL atualizado para usar a chave composta
    $sql = "DELETE FROM conversation_user WHERE user_id = :user_id AND conversation_id = :conversation_id";
    $stmt = $pdo->prepare($sql);

    // Bind dos parâmetros
    $stmt->bindParam(':user_id', $userIdToDelete, \PDO::PARAM_INT);
    $stmt->bindParam(':conversation_id', $conversationIdToDelete, \PDO::PARAM_INT);
    $stmt->execute();

    // Verificação do número de linhas afetadas
    if ($stmt->rowCount() > 0) {
        echo "Linha com user_id $userIdToDelete e conversation_id $conversationIdToDelete foi excluída com sucesso.";
    } else {
        echo "Nenhuma linha foi encontrada com user_id $userIdToDelete e conversation_id $conversationIdToDelete.";
    }

} catch (\PDOException $e) {
    echo "Erro no banco de dados: " . $e->getMessage();
}

