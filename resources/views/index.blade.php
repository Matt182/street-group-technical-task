<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Matt Bowden | Street Group Technical Task</title>
    </head>
    <body>
        <div class="form-container">
            <h2>Homeowner Name Parser</h2>
            <hr>
            <form method="POST" action="/parse" enctype="multipart/form-data">
                @csrf
                <label>Please select a .csv file to be parsed:</label>
                <input type="file" name="csv_file" accept=".csv" required>
                <button>Parse CSV</button>
            </form>
        </div>
        <div>
            @if (isset($parsedData))
                @foreach ($parsedData as $person)
                    @dump($person)
                @endforeach
            @else
                No data
            @endif
        </div>
    </body>
</html>

<style>
body {
    background: white;
}
.form-container {
    background: rgb(75, 75, 75);
    border: 1px solid black;
    border-radius: 5px;
    width: 500px;
    padding: 10px;
    margin: 0px auto;
}
h2 {
    font-family: Tahoma;
    color: white;
    margin: 0px 0px 10px 0px;
    width: 100%;
    text-align: center;
}
form {
    display: flex;
    flex-direction: column;
    gap: 10px;
}
label {
    color: white;
}
button {
    background: green;
    border: none;
    color: white;
    font-size: 18px;
    padding: 5px;
}
</style>
