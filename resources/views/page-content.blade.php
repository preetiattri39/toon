<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>{{ $page->title }}</title>
  <style>
    body {
      margin: 0;
      padding: 40px;
      font-family: Arial, sans-serif;
      background-color: #f5f5f5;
    }
    .card {
      max-width: 800px;
      margin: 0 auto;
      background: #fff;
      border: 1px solid #ddd;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      padding: 24px;
    }
    .card h1 {
      margin-top: 0;
      font-size: 28px;
      color: #333;
    }
    .card .subtitle {
      color: #666;
      margin-bottom: 16px;
    }
    .card .content {
      line-height: 1.6;
      color: #444;
    }
  </style>
</head>
<body>

  <div class="card">
    <h1>{{ $page->name }}</h1>
    

    <div class="content">
      {!! $page->page_content !!}
    </div>
  </div>

</body>
</html>
