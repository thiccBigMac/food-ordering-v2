<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Add Menu</title>
  <link href="/food-ordering/restaurant/styles/style.css" rel="stylesheet" />
  <style>
    /* Reset some default styles */
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f9f9f9;
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: flex-start;
      min-height: 100vh;
    }

    .main-content {
      background: white;
      margin: 40px auto;
      padding: 30px 40px;
      border-radius: 10px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
      max-width: 450px;
      width: 100%;
    }

    .topbar {
      font-size: 28px;
      font-weight: 700;
      color: #333;
      margin-bottom: 20px;
      text-align: center;
      letter-spacing: 1px;
    }

    form h1 {
      margin-bottom: 30px;
      font-weight: 700;
      color: #222;
      text-align: center;
    }

    form .input-box {
      margin-bottom: 20px;
      display: flex;
      flex-direction: column;
    }

    form .input-box label {
      font-weight: 600;
      margin-bottom: 8px;
      color: #555;
    }

    form .input-box input[type="text"],
    form .input-box input[type="file"] {
      padding: 12px 15px;
      font-size: 16px;
      border: 1.5px solid #ccc;
      border-radius: 6px;
      transition: border-color 0.3s ease;
      outline: none;
    }

    form .input-box input[type="text"]:focus,
    form .input-box input[type="file"]:focus {
      border-color: #09ff00ff;
      box-shadow: 0 0 6px rgba(0,123,255,0.3);
    }

    .btn {
      width: 100%;
      padding: 14px 0;
      font-size: 18px;
      font-weight: 700;
      color: white;
      background-color: #007BFF;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: background-color 0.25s ease;
      letter-spacing: 0.05em;
    }

    .btn:hover {
      background-color: #0056b3;
    }
  </style>
</head>
<body>

  <?php include "../auth/session.php"; ?>
  <?php include '../sidebar.php'; ?>

  <div class="main-content">
    <div class="topbar">Menu</div>
    <div class="content">

      <form action="add_process.php" method="POST" enctype="multipart/form-data">
        <h1>Add Menu</h1>
        
        <div class="input-box">
          <label for="name">Name</label>
          <input type="text" id="name" name="name" placeholder="Name" required />
        </div>

        <div class="input-box">
          <label for="description">Description</label>
          <input type="text" id="description" name="description" placeholder="Description" required />
        </div>

        <div class="input-box">
          <label for="price">Price</label>
          <input type="text" id="price" name="price" placeholder="Price" required />
        </div>

        <div class="input-box">
          <label for="image">Image</label>
          <input type="file" id="image" name="image" required />
        </div>

        <button type="submit" class="btn">Submit</button>
      </form>

    </div>
  </div>

</body>
</html>
