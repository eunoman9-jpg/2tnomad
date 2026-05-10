<?php
session_start();

require_once "dbconn.php";
require_once "utils.php";

$db = new Database();

// Retrieve all the products from the database
$products = $db->conn->query("SELECT * FROM products ORDER BY created_at DESC");

// Retrieve all users from the database
$users = $db->conn->query("SELECT * FROM users");

$action = "Add";


// AJAX handler for edit workflow
if (isset($_GET['id'])) {
    $product_id = $_GET['id'];
    $stmt = $db->conn->prepare("SELECT * FROM products WHERE product_id = ?");
    $stmt->bind_param("s", $product_id);
    $stmt->execute();

    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $product = $result->fetch_assoc();
    }

    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'product_id' => $product['product_id'],
        'name' => $product['name'],
        'description' => $product['description'],
        'price' => $product['price'],
        'stock' => $product['stock'],
        'image_url' => $product['image_url']
    ]);
    exit();
}

// Product form post handler for create and update
if (isset($_POST['action'])) {
    if ($_POST['action'] === "Delete") {
        $stmt = $db->conn->prepare("DELETE FROM products WHERE product_id=?");
        $stmt->bind_param("i", $_POST['delete_id']);
    } else {
        $id = $_POST['product_id'];
        $name  = trim($_POST['name']);
        $description = trim($_POST['description']);
        $price = $_POST['price'];
        $stock = $_POST['stock'];
        $image = trim($_POST['image_url']);

        if ($_POST['action'] === "Add") {
            $stmt = $db->conn->prepare(
                "INSERT INTO products (name, description, price, stock, category_id, image_url, created_at) VALUES (?, ?, ?, ?, 1, ?, NOW())"
            );
            $stmt->bind_param("ssdis", $name, $description, $price, $stock, $image);
        } else if ($_POST['action'] === "Edit") {
            $stmt = $db->conn->prepare(
                "UPDATE products SET name=?, description=?, price=?, stock=?, image_url=? WHERE product_id=?"
            );
            $stmt->bind_param("ssdisi", $name, $description, $price, $stock, $image, $id);
        }
    }

    if ($stmt->execute()) {
        $db->conn->commit();
        header("Location: index.php");
        exit();
    } else {
        echo "Error: " . $db->conn->error;
    }
}

// Handler for delete product

// Close connection when done
$db->close();

include_once("./include/header.php");
?>


<section class="container">
    <!-- Sidebar -->
    <section class="sidebar">
        <h3 style="color: #f1f1f1;">Database Management</h3>
        <nav class="sticky" id="nav-sidebar" aria-label="Sidebar" aria-expanded="true">
            <ul class="nav-items">
                <li class="nav-link">
                    <a href="#">Products</a>
                </li>
                <li class="nav-link">
                    <a href="#">Users</a>
                </li>
            </ul>
        </nav>
    </section>

    <!-- Main - Products & User Management -->
    <section class="main">
        <div class="title-row">
            <h2>Products</h2>
            <button onclick="openProductModal()">Add New Product</button>
        </div>
        <div class="table">

            <table id="data-table">
                <thead>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Actions</th>
                </thead>
                <tbody>
                    <?php
                    if ($products) {
                        while ($row = $products->fetch_assoc()) {
                    ?>
                            <tr>
                                <td><?php echo $row['product_id'] ?></td>
                                <td><?php echo truncateText($row['name']) ?></td>
                                <td><?php echo truncateText($row['description']) ?></td>
                                <td><?php echo $row['price'] ?></td>
                                <td><?php echo $row['stock'] ?></td>
                                <td>
                                    <div class="actions">
                                        <button data-id="<?php echo $row['product_id'] ?>" data-action="Edit" class="edit-btn">Edit</button>
                                        <button data-id="<?php echo $row['product_id'] ?>" data-action="Delete" class="delete-btn">Delete</button>
                                        <!-- onclick="openProductDeleteModal()" -->
                                    </div>
                                </td>
                            </tr>
                    <?php
                        }
                    }
                    ?>
                </tbody>
                <tfoot></tfoot>
            </table>
        </div>
    </section>

    <!-- Modal for adding or editing a product -->
    <div id="product-modal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeProductModal()">&times;</span>
            <h2 id="form-header"><?php echo $action ?> Product</h2>

            <form id="product-form" method="POST">
                <input type="hidden" name="action" id="action" value="<?php echo $action ?>">
                <input type="hidden" name="product_id" id="product_id">
                <input type="text" name="name" placeholder="Product Name" id="name">
                <textarea class="text" rows="6" name="description" id="description" placeholder="Product Description"></textarea>
                <div class="input-row">
                    <input type="number" name="price" id="price" placeholder="Product Price">
                    <input type="number" name="stock" id="stock" placeholder="Amount of stock in inventory">
                </div>
                <input type="text" name="image_url" id="image_url" placeholder="Product Image URL">
                <button class="login-text" type="submit">Submit</button>
            </form>
        </div>
    </div>

    <!-- Modal for confirming product deletion action -->
    <div id="delete-modal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeProductDeleteModal()">&times;</span>
            <p class="prompt">Are you sure you want to delete this product</p>

            <div class="delete-actions">
                <form id="product-delete-form" method="post">
                    <input type="hidden" name="action" id="action" value="Delete">
                    <input type="hidden" name="delete_id" id="delete_id">
                    <button type="submit" class="yes-btn">Yes</button>
                </form>
                <button onclick="closeProductDeleteModal()" class="cancel-btn">Cancel</button>
            </div>
        </div>
    </div>
</section>

<script>
    function openProductModal() {
        document.getElementById('product-modal').style.display = 'block';
    }

    function closeProductModal() {
        document.getElementById('product-modal').style.display = 'none';

        // Clear the form
        document.getElementById('action').value = "";
        document.getElementById('product_id').value = "";
        document.getElementById('name').value = "";
        document.getElementById('description').innerText = "";
        document.getElementById('price').value = "";
        document.getElementById('stock').value = "";
        document.getElementById('image_url').value = "";

        document.getElementById("form-header").innerText = "Add Product"
    }

    function openProductDeleteModal() {
        document.getElementById('delete-modal').style.display = 'block';
    }

    function closeProductDeleteModal() {
        document.getElementById('delete-modal').style.display = 'none';

        document.getElementById('delete_id').value = "";
        document.getElementById('action').value = "";
    }

    window.onclick = function(event) {
        let modal = document.getElementById('product-modal');
        if (event.target == modal) {
            modal.style.display = "none";
            // Clear the form
            document.getElementById('action').value = "";
            document.getElementById('product_id').value = "";
            document.getElementById('name').value = "";
            document.getElementById('description').innerText = "";
            document.getElementById('price').value = "";
            document.getElementById('stock').value = "";
            document.getElementById('image_url').value = "";

            document.getElementById("form-header").innerText = "Add Product"
        }
    }

    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const itemId = this.dataset.id;
            const action = this.dataset.action;

            fetch(`admin.php?id=${itemId}&action=${action}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Pre-populate form fields
                        document.getElementById('action').value = "Edit";
                        document.getElementById('product_id').value = data.product_id;
                        document.getElementById('name').value = data.name;
                        document.getElementById('description').innerText = data.description;
                        document.getElementById('price').value = parseFloat(data.price);
                        document.getElementById('stock').value = parseInt(data.stock);
                        document.getElementById('image_url').value = data.image_url;

                        // Reload page to reflect selections
                        // location.reload();
                        document.getElementById("form-header").innerText = "Edit Product"
                        openProductModal();
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    });

    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const itemId = this.dataset.id;
            // const action = this.dataset.action;

            openProductDeleteModal();

            // Find the DOM element and add the delete id value
            document.getElementById('delete_id').value = itemId;
            // document.getElementById('action').value = action;
        });
    });

    // document.addEventListener('DOMContentLoaded', function() {
    //     const editBtn = document.querySelector(".edit-btn");

    //     editBtn.addEventListener('click', () => {

    //     });
    // });
</script>

<script src="./script.js"></script>

<?php include_once('./include/footer.php'); ?>