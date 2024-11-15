<?php
    require_once 'config.php';

    // Funções auxiliares
    function getTarefas($conn) {
        $sql = "SELECT * FROM tarefas ORDER BY ordem ASC";
        $result = $conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    function getMaxOrdem($conn) {
        $sql = "SELECT MAX(ordem) as max_ordem FROM tarefas";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        return $row['max_ordem'] ?? 0;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Tarefas</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .task-list { width: 100%; border-collapse: collapse; }
        .task-list th, .task-list td { padding: 10px; border: 1px solid #ddd; }
        .task-list tr:nth-child(even) { background-color: #f9f9f9; }
        .expensive-task { background-color: #fff3cd !important; }
        .actions { display: flex; gap: 10px; }
        .btn { padding: 5px 10px; cursor: pointer; }
        .modal { display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%);
                background: white; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1);  z-index: 1001; }
        .overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
                background: rgba(0,0,0,0.5);  z-index: 1000; }
    </style>
</head>
<body>
    <h1>Lista de Tarefas</h1>
    
    <table class="task-list">
        <thead>
            <tr>
                <th>Nome da Tarefa</th>
                <th>Custo (R$)</th>
                <th>Data Limite</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody id="taskList">
            <?php
            $tarefas = getTarefas($conn);
            foreach ($tarefas as $tarefa) {
                $class = $tarefa['custo'] >= 1000 ? 'expensive-task' : '';
                echo "<tr class='$class' data-id='{$tarefa['id']}' data-ordem='{$tarefa['ordem']}'>";
                echo "<td>{$tarefa['nome']}</td>";
                echo "<td>R$ " . number_format($tarefa['custo'], 2, ',', '.') . "</td>";
                echo "<td>" . date('d/m/Y', strtotime($tarefa['data_limite'])) . "</td>";
                echo "<td class='actions'>";
                echo "<button onclick='editarTarefa({$tarefa['id']})' class='btn'><i class='fas fa-edit'></i></button>";
                echo "<button onclick='excluirTarefa({$tarefa['id']})' class='btn'><i class='fas fa-trash'></i></button>";
                echo "<button onclick='moverTarefa({$tarefa['id']}, \"up\")' class='btn'><i class='fas fa-arrow-up'></i></button>";
                echo "<button onclick='moverTarefa({$tarefa['id']}, \"down\")' class='btn'><i class='fas fa-arrow-down'></i></button>";
                echo "</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <button onclick="showAddModal()" class="btn" style="margin-top: 20px;">
        <i class="fas fa-plus"></i> Nova Tarefa
    </button>

    <!-- Modal de Adição -->
    <div id="addModal" class="modal">
        <h2>Nova Tarefa</h2>
        <form id="addForm">
            <div>
                <label>Nome:</label>
                <input type="text" name="nome" required>
            </div>
            <div>
                <label>Custo (R$):</label>
                <input type="number" step="0.01" name="custo" required>
            </div>
            <div>
                <label>Data Limite:</label>
                <input type="date" name="data_limite" required>
            </div>
            <button type="submit">Salvar</button>
            <button type="button" onclick="closeModal('addModal')">Cancelar</button>
        </form>
    </div>

    <!-- Modal de Edição -->
    <div id="editModal" class="modal">
        <h2>Editar Tarefa</h2>
        <form id="editForm">
            <input type="hidden" name="id">
            <div>
                <label>Nome:</label>
                <input type="text" name="nome" required>
            </div>
            <div>
                <label>Custo (R$):</label>
                <input type="number" step="0.01" name="custo" required>
            </div>
            <div>
                <label>Data Limite:</label>
                <input type="date" name="data_limite" required>
            </div>
            <button type="submit">Salvar</button>
            <button type="button" onclick="closeModal('editModal')">Cancelar</button>
        </form>
    </div>

    <div class="overlay" id="overlay"></div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#taskList").sortable({
                update: function(event, ui) {
                    const newOrder = [];
                    $("#taskList tr").each(function(index) {
                        newOrder.push({
                            id: $(this).data('id'),
                            ordem: index + 1
                        });
                    });
                    
                    $.ajax({
                        url: 'actions.php',
                        method: 'POST',
                        data: {
                            action: 'reorder',
                            order: newOrder
                        },
                        success: function(response) {
                            location.reload();
                        }
                    });
                }
            });
        });

        function showAddModal() {
            document.getElementById('overlay').style.display = 'block';
            document.getElementById('addModal').style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById('overlay').style.display = 'none';
            document.getElementById(modalId).style.display = 'none';
        }

        function editarTarefa(id) {
            $.ajax({
                url: 'actions.php',
                method: 'GET',
                data: {
                    action: 'get',
                    id: id
                },
                success: function(response) {
                    const tarefa = JSON.parse(response);
                    const form = document.getElementById('editForm');
                    form.id.value = tarefa.id;
                    form.nome.value = tarefa.nome;
                    form.custo.value = tarefa.custo;
                    form.data_limite.value = tarefa.data_limite;
                    
                    document.getElementById('overlay').style.display = 'block';
                    document.getElementById('editModal').style.display = 'block';
                }
            });
        }

        function excluirTarefa(id) {
            if (confirm('Deseja realmente excluir esta tarefa?')) {
                $.ajax({
                    url: 'actions.php',
                    method: 'POST',
                    data: {
                        action: 'delete',
                        id: id
                    },
                    success: function(response) {
                        location.reload();
                    }
                });
            }
        }

        function moverTarefa(id, direction) {
    $.ajax({
        url: 'actions.php',
        method: 'POST',
        data: {
            action: 'move',
            id: id,
            direction: direction
        },
        success: function(response) {
            if (response === 'success') {
                // Atualize a lista de tarefas sem recarregar a página
                // Você pode reordenar as tarefas no DOM ou fazer um novo carregamento via AJAX
                location.reload();  // Caso queira recarregar a página inteira
            } else {
                alert('Erro ao mover a tarefa: ' + response);
            }
        }
    });
}

        document.getElementById('addForm').onsubmit = function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'add');
            
            $.ajax({
                url: 'actions.php',
                method: 'POST',
                data: Object.fromEntries(formData),
                success: function(response) {
                    if (response === 'exists') {
                        alert('Já existe uma tarefa com este nome!');
                    } else {
                        location.reload();
                    }
                }
            });
        };

        document.getElementById('editForm').onsubmit = function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'edit');
            
            $.ajax({
                url: 'actions.php',
                method: 'POST',
                data: Object.fromEntries(formData),
                success: function(response) {
                    if (response === 'exists') {
                        alert('Já existe uma tarefa com este nome!');
                    } else {
                        location.reload();
                    }
                }
            });
        };
    </script>
</body>
</html>