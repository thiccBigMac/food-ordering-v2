<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Add Menu</title>
  <link href="/food-ordering/restaurant/styles/style.css" rel="stylesheet" />
   <style>
    @import url('https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=Work+Sans:wght@400;500;600;700&display=swap');

    body {
      font-family: 'Work Sans', 'Segoe UI', sans-serif;
      background-color: #FBF7EF;
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
      padding: 34px 40px;
      border-radius: 18px;
      box-shadow: 0 10px 30px rgba(43,38,32,0.12);
      max-width: 450px;
      width: 100%;
    }

    .topbar {
      font-family: 'Fraunces', Georgia, serif;
      font-size: 28px;
      font-weight: 700;
      color: #ffffff;
      background: #2E4E50;
      margin-bottom: 20px;
      text-align: center;
      letter-spacing: 0.5px;
    }

    form h1 {
      font-family: 'Fraunces', Georgia, serif;
      margin-bottom: 26px;
      font-weight: 700;
      color: #2E4E50;
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
      color: #6B6355;
      font-size: 14px;
    }

    form .input-box input[type="text"],
    form .input-box input[type="file"] {
      padding: 12px 15px;
      font-size: 15px;
      color: #2B2620;
      background-color: #FBF7EF;
      border: 1.5px solid #e6ddc9;
      border-radius: 8px;
      transition: border-color 0.3s ease, box-shadow 0.3s ease;
      outline: none;
    }

    form .input-box input[type="text"]:focus,
    form .input-box input[type="file"]:focus {
      border-color: #D9A441;
      box-shadow: 0 0 6px rgba(217,164,65,0.35);
    }

    .btn {
      width: 100%;
      padding: 14px 0;
      font-size: 16px;
      font-weight: 700;
      color: #2B2620;
      background-color: #D9A441;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      transition: background-color 0.25s ease, color 0.25s ease;
      letter-spacing: 0.03em;
    }

    .btn:hover {
      background-color: #2E4E50;
      color: white;
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
