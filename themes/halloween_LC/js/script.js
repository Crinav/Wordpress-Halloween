(function() {
  var item = document.getElementById("h1_scream");
var music = new Audio('http://localhost/wordpress/wp-content/uploads/2020/06/Humi1013.mp3');

//item.addEventListener("mouseover", playMusic, false);
//item.addEventListener("mouseout", stopMusic, false);

function playMusic() {
   music.play();
}

function stopMusic() {
   music.stop();
}
let del = document.getElementById('del');
del.addEventListener("click", function(e){
   e.preventDefault();
   alert('rrr')
});

})();

/*var buttonQuestion = document.getElementById("button_Pose_Question");
console.log("ok");
console.log(buttonQuestion);

buttonQuestion.addEventListener("click",showDiv);

function showDiv(){
   console.log('ici');
   document.getElementById("add_text").innerHTML = "Hello Dolly.";
}*/
