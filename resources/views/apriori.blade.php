<script>
      var produces = {{ $products}}  
      window.onload = function() {
        var produce1 = document.getElementById("produce1");
        var produce2 = document.getElementById("produce2");
        var produce3 = document.getElementById("produce3");
        for (var x in produces) {
            produce1.options[produce1.options.length] = new Option(x, x);
        }
        produce1.onchange = function() {
          //empty Chapters- and Topics- dropdowns
          produce2.length = 1;
          produce3.length = 1;
          //display correct values
          for (var y in produces) {
            if (y !== produce1.options) {
                produce2.options[produce2.options.length] = new Option(y, y);
            }
          }
        }
        produce2.onchange = function() {
          //empty Chapters dropdown
          produce3.length = 1;
          //display correct values
          for (var z in produces) {
            if (z !== produce1.options && z !== produce2.options) {
                produce3.options[produce3.options.length] = new Option(z[i], z[i]);
            }
          }
        }
      }
      </script>

var xValues = { ! $items ! };
var yValues = { !$lift ! };