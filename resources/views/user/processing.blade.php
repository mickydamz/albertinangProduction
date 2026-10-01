@extends('layouts.app')

@section('title', __("Documents are being reviewed"))

@section('content')
<style>
.progress-container {
  display: flex;
  justify-content: center;
  align-items: center;
  flex-direction: column;
  height: 100vh; /* Adjust as needed */
}

#myProgress {
  width: 100%;
  height: 20px;
  background-color: #f2f2f2;
}

#myProgress::-webkit-progress-value {
  background-color: #4CAF50;
}

#myProgress::-moz-progress-bar {
  background-color: #4CAF50;
}

h1 {
  text-align: center;
}
</style>

<div class="progress-container">
  <h1>Processing</h1>
  <progress id="myProgress" value="0" max="100"></progress>
</div>
<script src="https://code.jquery.com/jquery-1.12.1.min.js"></script>
<script>
var timer;
var progress;
var max;
$(document).ready(function() {
  progress = $("#myProgress");
  max = progress.attr("max");
  timer = setInterval(updateBar, 1000);
});
function updateBar() {
  var current = progress.attr("value");
  current += 1;
  progress.attr("value", current);
  if (current >= max) {
    clearInterval(timer);
  }
}
</script>


   @if(isset($redirect) && $redirect)
       <script>
           setTimeout(function() {
               window.location.href = "await-success";
           }, 4000);
       </script>
   @endif

@endsection
