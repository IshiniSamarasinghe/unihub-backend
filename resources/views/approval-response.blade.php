<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Event Approval Response</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      background-color: #f8f9fa;
      margin: 0;
    }
    .box {
      background: white;
      padding: 40px 30px;
      border-radius: 12px;
      box-shadow: 0 0 18px rgba(0,0,0,0.08);
      text-align: center;
    }
    h2 {
      color: {{ $status === 'success' ? '#28a745' : ($status === 'rejected' ? '#dc3545' : '#6c757d') }};
      margin-bottom: 10px;
    }
    p {
      color: #343a40;
    }
  </style>
</head>
<body>
  <div class="box">
    <h2>{{ $message }}</h2>
    <p>You may now close this window.</p>
  </div>
</body>
</html>
