<html>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
<body>
<style>
body {
  margin: 0;
  padding: 0;
  font-family: Arial, Helvetica, sans-serif;
  background: #fff url(bg-texture.png) repeat;
  color: #111;
}

h1, h3 {
  margin: 0;
  padding: 0;
  font-weight: normal;
}

a {
  color: green;
}

div#container {
  width: 580px;
  margin: 100px auto 0 auto;
  padding: 20px;
  background: #fff;
  border: 1px solid #1a1a1a;
}

/* HOVER STYLES */
div#pop-up {
  display: none;
  position: absolute;
  width: 280px;
  padding: 10px;
  background: #eeeeee;
  color: #000000;
  border: 1px solid #1a1a1a;
  font-size: 90%;
}
</style>

  <div id="container">
    <h1>Test 1</h1>
    <p>
      <a href="#" id="trigger">To show hidden div, simply hover your mouse over this link</a>.
    </p>

    <!-- HIDDEN / POP-UP DIV -->
    <div id="pop-up">
      <h3>Pop-up div Successfully Displayed</h3>
      <p>
        This div only appears when the trigger link is hovered over.
        Otherwise it is hidden from view.
      </p>
    </div>

  </div>

<script>
$(function() {
  var moveLeft = 1;
  var moveDown = 1;
  
  var boxX = 0;
  var boxY = 0;

  $('#trigger').hover(function(e) 
  {
	  boxX = $(this).css('left');
	  boxX += 1000;
	  boxY = $(this).css('top');
      $('#pop-up').show();
  }, function() 
  {
      $('#pop-up').hide();
  });
  
  $('#trigger').mousemove(function(e) 
  {
      $("#pop-up").css('top', boxY + moveDown).css('left', boxX + moveLeft);
  });

  
});
</script>

</body>
</html>