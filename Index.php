<?php
session_start();

// Inisialisasi session untuk menyimpan to-do list
if (!isset($_SESSION['todos'])) {
    $_SESSION['todos'] = [];
}

// Tambahkan item ke to-do list
if (isset($_POST['add_todo'])) {
    $todo_item = [
        'name' => $_POST['todo_name'],
        'priority' => $_POST['priority'],
        'description' => $_POST['description'],
        'status' => 'Not Completed'
    ];

    if (!empty($todo_item['name']) && !empty($todo_item['priority'])) {
        $_SESSION['todos'][] = $todo_item;
    }
}

// Hapus item dari to-do list
if (isset($_GET['delete'])) {
    $index = $_GET['delete'];
    unset($_SESSION['todos'][$index]);
    $_SESSION['todos'] = array_values($_SESSION['todos']); // Reindex array setelah penghapusan
}

// Ubah status task
if (isset($_GET['toggle_status'])) {
    $index = $_GET['toggle_status'];
    $_SESSION['todos'][$index]['status'] = $_SESSION['todos'][$index]['status'] === 'Completed' ? 'Not Completed' : 'Completed';
}

// Edit item di to-do list
if (isset($_POST['update_todo'])) {
    $index = $_POST['index'];
    $updated_todo = [
        'name' => $_POST['todo_name'],
        'priority' => $_POST['priority'],
        'description' => $_POST['description'],
        'status' => $_POST['status']
    ];
    $_SESSION['todos'][$index] = $updated_todo;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script>
        function confirmDelete(index) {
            if (confirm("Are you sure you want to delete this task?")) {
                window.location.href = "index.php?delete=" + index;
            }
        }
    </script>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">To-Do List</h2>

        <!-- Form Tambah To-Do -->
        <form method="POST" action="">
            <div class="form-group">
                <label for="todo_name">Task Name</label>
                <input type="text" class="form-control" id="todo_name" name="todo_name" placeholder="Enter task name" required>
            </div>
            <div class="form-group">
                <label for="priority">Priority</label>
                <select class="form-control" id="priority" name="priority" required>
                    <option value="Low">Low</option>
                    <option value="Medium">Medium</option>
                    <option value="High">High</option>
                </select>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3" placeholder="Enter task description"></textarea>
            </div>
            <button type="submit" class="btn btn-primary" name="add_todo">Add Task</button>
        </form>

        <!-- Daftar To-Do -->
        <?php if (!empty($_SESSION['todos'])): ?>
            <ul class="list-group mt-4">
                <?php foreach ($_SESSION['todos'] as $index => $todo): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong><?= htmlspecialchars($todo['name']) ?> (<?= htmlspecialchars($todo['priority']) ?>)</strong><br>
                            <small><?= htmlspecialchars($todo['description']) ?></small><br>
                            <span>Status: <?= htmlspecialchars($todo['status']) ?></span>
                        </div>
                        <div>
                            <button class="btn btn-success btn-sm" onclick="window.location.href='index.php?toggle_status=<?= $index ?>'"><?= $todo['status'] === 'Completed' ? 'Mark as Incomplete' : 'Mark as Completed' ?></button>
                            <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editModal<?= $index ?>">Edit</button>
                            <button class="btn btn-danger btn-sm" onclick="confirmDelete(<?= $index ?>)">Delete</button>
                        </div>
                    </li>

                    <!-- Modal Edit -->
                    <div class="modal fade" id="editModal<?= $index ?>" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editModalLabel">Edit Task</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form method="POST" action="">
                                    <div class="modal-body">
                                        <input type="hidden" name="index" value="<?= $index ?>">
                                        <div class="form-group">
                                            <label for="todo_name">Task Name</label>
                                            <input type="text" class="form-control" id="todo_name" name="todo_name" value="<?= htmlspecialchars($todo['name']) ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="priority">Priority</label>
                                            <select class="form-control" id="priority" name="priority" required>
                                                <option value="Low" <?= $todo['priority'] == 'Low' ? 'selected' : '' ?>>Low</option>
                                                <option value="Medium" <?= $todo['priority'] == 'Medium' ? 'selected' : '' ?>>Medium</option>
                                                <option value="High" <?= $todo['priority'] == 'High' ? 'selected' : '' ?>>High</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="description">Description</label>
                                            <textarea class="form-control" id="description" name="description" rows="3" required><?= htmlspecialchars($todo['description']) ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select class="form-control" id="status" name="status">
                                                <option value="Not Completed" <?= $todo['status'] == 'Not Completed' ? 'selected' : '' ?>>Not Completed</option>
                                                <option value="Completed" <?= $todo['status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary" name="update_todo">Save changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="text-center mt-4">No tasks found. Add a new task above.</p>
        <?php endif; ?>
    </div>

    <!-- Script Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
