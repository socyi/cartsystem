<!doctype html>
<html lang="en">
<head>
    @vite(['resources/js/app.js'])
    @vite(['resources/css/app.css'])

    <meta name="session_id" content="{{session()->getId()}}">



</head>
<body>
<div id="app">

</div>

</body>
</html>
