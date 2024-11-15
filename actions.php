<?php
    require_once 'config.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'add':
                $nome = $_POST['nome'];
                $custo = $_POST['custo'];
                $data_limite = $_POST['data_limite'];
                
                // Verificar se já existe uma tarefa com este nome
                $stmt = $conn->prepare("SELECT id FROM tarefas WHERE nome = ?");
                $stmt->bind_param("s", $nome);
                $stmt->execute();
                if ($stmt->get_result()->num_rows > 0) {
                    echo 'exists';
                    exit;
                }
                
                // Obter a próxima ordem
                $ordem = getMaxOrdem($conn) + 1;
                
                $stmt = $conn->prepare("INSERT INTO tarefas (nome, custo, data_limite, ordem) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("sdsi", $nome, $custo, $data_limite, $ordem);
                $stmt->execute();
                break;
                
            case 'edit':
                $id = $_POST['id'];
                $nome = $_POST['nome'];
                $custo = $_POST['custo'];
                $data_limite = $_POST['data_limite'];
                
                // Verificar se já existe uma tarefa com este nome (exceto a atual)
                $stmt = $conn->prepare("SELECT id FROM tarefas WHERE nome = ? AND id != ?");
                $stmt->bind_param("si", $nome, $id);
                $stmt->execute();
                if ($stmt->get_result()->num_rows > 0) {
                    echo 'exists';
                    exit;
                }
                
                $stmt = $conn->prepare("UPDATE tarefas SET nome = ?, custo = ?, data_limite = ? WHERE id = ?");
                $stmt->bind_param("sdsi", $nome, $custo, $data_limite, $id);
                $stmt->execute();
                break;
                
            case 'delete':
                $id = $_POST['id'];
                $stmt = $conn->prepare("DELETE FROM tarefas WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                break;
                
                case 'move':
                    $id = $_POST['id'];
                    $direction = $_POST['direction'];
                
                    // Recupera a ordem atual da tarefa
                    $stmt = $conn->prepare("SELECT ordem FROM tarefas WHERE id = ?");
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $current_ordem = $result->fetch_assoc()['ordem'];
                
                    // Verifica a direção e calcula a nova ordem
                    if ($direction === 'up' && $current_ordem > 1) {
                        $new_ordem = $current_ordem - 1;
                    } else if ($direction === 'down') {
                        // Verifica a maior ordem para garantir que não vá além do limite
                        $stmt = $conn->prepare("SELECT MAX(ordem) AS max_ordem FROM tarefas");
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $max_ordem = $result->fetch_assoc()['max_ordem'];
                
                        // Verifica se a tarefa não está na última posição
                        if ($current_ordem < $max_ordem) {
                            $new_ordem = $current_ordem + 1;
                        } else {
                            // Não faz nada se já estiver na última posição
                            echo 'error: already at the bottom';
                            exit();
                        }
                    } else {
                        // Se não for nenhuma das direções válidas, termina
                        break;
                    }
                
                    // Trocar ordem com a tarefa adjacente
                    $conn->begin_transaction();
                    try {
                        // A primeira etapa é "marcar" a ordem da tarefa atual como um valor temporário
                        $stmt = $conn->prepare("UPDATE tarefas SET ordem = 0 WHERE id = ?");
                        $stmt->bind_param("i", $id);
                        $stmt->execute();
                
                        // Agora, troca a ordem entre as duas tarefas (ajustando a tarefa adjacente)
                        $stmt = $conn->prepare("UPDATE tarefas SET ordem = ? WHERE ordem = ?");
                        $stmt->bind_param("ii", $current_ordem, $new_ordem);
                        $stmt->execute();
                
                        // Finalmente, define a nova ordem da tarefa movida
                        $stmt = $conn->prepare("UPDATE tarefas SET ordem = ? WHERE id = ?");
                        $stmt->bind_param("ii", $new_ordem, $id);
                        $stmt->execute();
                
                        // Se tudo ocorrer bem, confirma a transação
                        $conn->commit();
                        echo 'success';  // Retorna sucesso
                    } catch (Exception $e) {
                        // Em caso de erro, faz o rollback da transação
                        $conn->rollback();
                        echo 'error: ' . $e->getMessage();  // Exibe a mensagem de erro para depuração
                    }
                    break;
                
            case 'reorder':
                $newOrder = $_POST['order'];
                
                $conn->begin_transaction();
                try {
                    foreach ($newOrder as $item) {
                        $stmt = $conn->prepare("UPDATE tarefas SET ordem = ? WHERE id = ?");
                        $stmt->bind_param("ii", $item['ordem'], $item['id']);
                        $stmt->execute();
                    }
                    $conn->commit();
                } catch (Exception $e) {
                    $conn->rollback();
                }
                break;
        }
    } else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $action = $_GET['action'] ?? '';
        
        switch ($action) {
            case 'get':
                $id = $_GET['id'];
                $stmt = $conn->prepare("SELECT id, nome, custo, DATE_FORMAT(data_limite, '%Y-%m-%d') as data_limite FROM tarefas WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                $tarefa = $result->fetch_assoc();
                echo json_encode($tarefa);
                break;
                
            case 'list':
                $stmt = $conn->prepare("SELECT id, nome, custo, DATE_FORMAT(data_limite, '%Y-%m-%d') as data_limite, ordem FROM tarefas ORDER BY ordem ASC");
                $stmt->execute();
                $result = $stmt->get_result();
                $tarefas = $result->fetch_all(MYSQLI_ASSOC);
                echo json_encode($tarefas);
                break;
        }
    }

    // Função auxiliar para obter a maior ordem
    function getMaxOrdem($conn) {
        $sql = "SELECT MAX(ordem) as max_ordem FROM tarefas";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        return $row['max_ordem'] ?? 0;
    }
?>