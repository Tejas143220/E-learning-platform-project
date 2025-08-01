<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.html");
    exit();
}

$all_courses = $conn->query("SELECT * FROM courses ORDER BY id DESC");
$user_id = $_SESSION['user_id'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>E-Learning Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
  <style>
    body {
      background-color: #f0f4f8;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    header {
      background-color:#2c3e50;
      color: white;
      padding: 1.25rem 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
    }
    header h2 {
      margin: 0;
      font-weight: 600;
    }
    .btn-logout {
      background: white;
      color: #4a90e2;
      font-weight: 600;
      border: none;
      padding: 0.5rem 1.25rem;
      border-radius: 0.375rem;
      transition: background-color 0.3s ease;
      text-decoration: none;
    }
    .btn-logout:hover {
      background-color: #357abd;
      color: white;
    }
  </style>
</head>
<body>

<header>
  <h2>Welcome, <?php echo htmlspecialchars($_SESSION['user']); ?>!</h2>
  <a href="logout.php" class="btn-logout">Logout</a>
</header>

<div class="container my-5">

  <?php if (isset($_GET['msg'])): ?>
    <div class="mb-4">
      <?php
      if ($_GET['msg'] === 'purchase_success') {
          echo '<div class="alert alert-success">Course purchased successfully!</div>';
      } elseif ($_GET['msg'] === 'already_purchased') {
          echo '<div class="alert alert-info">You have already purchased this course.</div>';
      } elseif ($_GET['msg'] === 'purchase_failed') {
          echo '<div class="alert alert-danger">Failed to complete purchase. Please try again.</div>';
      }
      ?>
    </div>
  <?php endif; ?>

  <!-- Free Courses -->
  <section>
    <h3 class="mb-4 text-primary">📚 Free Courses</h3>
    <div class="row row-cols-1 row-cols-md-3 g-4">
      <?php
      $courses = $conn->query("SELECT * FROM courses WHERE is_paid = 0 ORDER BY id DESC");
      if ($courses->num_rows === 0) {
          echo '<p>No free courses available yet.</p>';
      } else {
          while ($row = $courses->fetch_assoc()) {
              $video_url = isset($row['video_url']) ? htmlspecialchars($row['video_url']) : '';
              echo '
              <div class="col">
                <div class="card h-100 shadow-sm">
                  <div class="ratio ratio-16x9">
                    <iframe src="' . $video_url . '" allowfullscreen></iframe>
                  </div>
                  <div class="card-body">
                    <h5 class="card-title text-success">' . htmlspecialchars($row['title']) . '</h5>
                    <p>' . htmlspecialchars($row['description']) . '</p>
                  </div>
                </div>
              </div>';
          }
      }
      ?>
    </div>
  </section>

  <!-- Paid Courses -->
  <section class="mt-5">
    <h3 class="mb-4 text-primary">💰 Paid Courses</h3>
    <div class="row row-cols-1 row-cols-md-3 g-4">
      <?php
      $paid_courses = $conn->query("SELECT * FROM courses WHERE is_paid = 1 ORDER BY id DESC");
      if ($paid_courses->num_rows === 0) {
          echo '<p>No paid courses available yet.</p>';
      } else {
          while ($row = $paid_courses->fetch_assoc()) {
              $video_url = isset($row['video_url']) ? htmlspecialchars($row['video_url']) : '';
              $course_id = htmlspecialchars($row['id']);
              $price = (int)$row['price'] * 100;
              echo '
              <div class="col">
                <div class="card h-100 shadow-sm border-warning">
                  <div class="ratio ratio-16x9">
                    <iframe src="' . $video_url . '" allowfullscreen></iframe>
                  </div>
                  <div class="card-body">
                    <h5 class="card-title text-warning">' . htmlspecialchars($row['title']) . '</h5>
                    <p>' . htmlspecialchars($row['description']) . '</p>
                    <span class="badge bg-success">₹' . htmlspecialchars($row['price']) . '</span>
                    <button class="btn btn-sm btn-success mt-3" onclick="buyCourse(' . $price . ', ' . $course_id . ')">Buy Now</button>
                  </div>
                </div>
              </div>';
          }
      }
      ?>
    </div>
  </section>

  <!-- Add Course -->
  <section class="mt-5">
    <h3>➕ Add New Course</h3>
    <form action="add_course.php" method="POST" class="row g-3">
      <div class="col-md-6">
        <input type="text" name="title" class="form-control" placeholder="Course Title" required />
      </div>
      <div class="col-md-6">
        <input type="url" name="video_url" class="form-control" placeholder="YouTube Embed URL" required />
      </div>
      <div class="col-12">
        <textarea name="description" rows="3" class="form-control" placeholder="Course Description" required></textarea>
      </div>
      <div class="col-md-3 d-flex align-items-center">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="is_paid" value="1" />
          <label class="form-check-label">Paid Course</label>
        </div>
      </div>
      <div class="col-md-3">
        <input type="number" name="price" class="form-control" placeholder="Price (₹)" min="0" />
      </div>
      <div class="col-md-6 d-grid">
        <button type="submit" class="btn btn-primary">Add Course</button>
      </div>
    </form>
  </section>

  <!-- Update Course -->
  <section class="mt-5">
    <h3>✏️ Update Course</h3>
    <form action="update_course.php" method="POST" class="row g-3">
      <div class="col-md-6">
        <select name="course_id" class="form-select" required>
          <option value="" disabled selected>Select course to update</option>
          <?php foreach ($all_courses as $row): ?>
            <option value="<?= htmlspecialchars($row['id']) ?>"><?= htmlspecialchars($row['title']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-6">
        <input type="text" name="title" class="form-control" placeholder="New Course Title" />
      </div>
      <div class="col-md-6">
        <input type="url" name="video_url" class="form-control" placeholder="New Video URL" />
      </div>
      <div class="col-md-6">
        <input type="number" name="price" class="form-control" placeholder="New Price (₹)" min="0" />
      </div>
      <div class="col-12">
        <textarea name="description" rows="3" class="form-control" placeholder="New Description"></textarea>
      </div>
      <div class="col-md-3 d-flex align-items-center">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="is_paid" value="1" />
          <label class="form-check-label">Paid Course</label>
        </div>
      </div>
      <div class="col-md-9 d-grid">
        <button type="submit" class="btn btn-warning">Update Course</button>
      </div>
    </form>
  </section>

  <!-- Remove Course -->
  <section class="mt-5 mb-5">
    <h3>❌ Remove Course</h3>
    <form action="remove_course.php" method="POST" class="row g-3">
      <div class="col-md-8">
        <select name="course_id" class="form-select" required>
          <option value="" disabled selected>Select course to remove</option>
          <?php foreach ($all_courses as $row): ?>
            <option value="<?= htmlspecialchars($row['id']) ?>"><?= htmlspecialchars($row['title']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-4 d-grid">
        <button type="submit" class="btn btn-danger">Remove Course</button>
      </div>
    </form>
  </section>

</div>

<script>
function buyCourse(amount, courseId) {
  fetch('create_order.php?course_id=' + courseId)
    .then(res => res.json())
    .then(data => {
      const options = {
        key: 'YOUR_KEY_ID',
        amount: data.amount,
        currency: 'INR',
        name: 'E-Learning Platform',
        description: 'Course Purchase',
        order_id: data.id,
        handler: function (response) {
          fetch('verify_payment.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              razorpay_payment_id: response.razorpay_payment_id,
              razorpay_order_id: response.razorpay_order_id,
              razorpay_signature: response.razorpay_signature,
              course_id: courseId
            })
          }).then(res => res.json())
            .then(data => {
              if (data.status === 'success') {
                window.location.href = 'dashboard.php?msg=purchase_success';
              } else {
                alert("Payment failed. Please try again.");
              }
            });
        }
      };
      const rzp = new Razorpay(options);
      rzp.open();
    });
}
</script>

</body>
</html>
