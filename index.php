<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" media="screen" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.5/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css?family=Indie+Flower|Montserrat" rel="stylesheet">
    <title>Catch Baby Dragon</title>
    <link href="css/default.css" rel="stylesheet">


    <!--Catch Baby Dragon @enoxh  -->
</head>
<body>
    <div class="container">
        <div class="col-sm-6 col-sm-offset-3">
            <div class="panel panel-default">
                <div class="panel-body">
                    <h2 class="text-center title"><img src="img/player.png"/> Catch Baby Dragon</h2>
                    <div class="container" id="cont" style="width:400px;">
                        <div class="row">
                            <span class="points" id="showPoints">Points: 0</span>
                            <span id="showCounter" class="time">Time Elapsed: 0</span>
                            <table class="maptable">
                                <tr>
                                    <td id="cell_00"></td>
                                    <td id="cell_01"></td>
                                    <td id="cell_02"></td>
                                    <td id="cell_03"></td>
                                </tr>
                                <tr>
                                    <td id="cell_10"></td>
                                    <td id="cell_11"></td>
                                    <td id="cell_12"></td>
                                    <td id="cell_13"></td>
                                </tr>
                                <tr>
                                    <td id="cell_20"></td>
                                    <td id="cell_21"></td>
                                    <td id="cell_22"></td>
                                    <td id="cell_23"></td>
                                </tr>
                                <tr>
                                    <td id="cell_30"></td>
                                    <td id="cell_31"></td>
                                    <td id="cell_32"></td>
                                    <td id="cell_33"></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div id="startBtn" class="text-center">
                        <br/>
                        <button class="btn btn-primary btn-lg center-block" onclick="startGame()">START</button>
                        <br/>
                        <p><img src="img/enCrab.png">Baby dragon just learned to teleport!</p>
                        <p>Catch baby dragon as many times as you can before the timer reaches 20.</p>
                        <p>Use the keyboard to play. W, A, S and D are the movement keys. W is up. D is right, S down and A is left.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script src="js/keypress.min.js"></script>

    <script>
        //insantiate keypress.js
        var listener = new window.keypress.Listener();
        var counter = 0;
        var last = 0;
        var points = 0;
        var started = 0;
        //player position  globals      
        var px = 0;
        var py = 0;
        var playing = false;
        //enemyposition  globals      
        var enx = 0;
        var eny = 0;
        var roundLngth = 2000;
        //START GAME LOOP
        var lastRender = 0;;
        window.requestAnimationFrame(loop);
        //Last Random Event 
        var lastRndEvt = 0;
        var nextRndEvt = 0;
        var roundOver = 10;
        // Starting map       
        var map = [
            [0, 0, 0, 0],
            [0, 0, 0, 0],
            [0, 0, 0, 0],
            [0, 0, 0, 0]
        ];

        //START GAME
        function startGame() {
            counter = 0;
            playing = true;
            started = counter;
            setPlayerPos(2, 3);
            setEnemies();
            drawPoints();
            document.getElementById('startBtn').innerHTML = '';
        }

        // CORE FUNCS

        //SETS THE PLAYER POSITION        
        function setPlayerPos(x, y) {
            map[x][y] = 1;
            console.log('player position set');
            py = y;
            px = x;
            findPlayer();
            drawPlayer();
        }


        //FINDS THE PLAYERS POSITION       
        function findPlayer() {
            for (var i = 0; i < map.length; i++) {
                for (var j = 0; j < map[i].length; j++) {
                    if (map[i][j] == 1) {
                        console.log('player found at ' + py + ', ' + px + ' ');
                    }
                }
            }
        }




        //DRAW PLAYER ON MAP      
        function drawPlayer() {
            console.log('drawing');
            if(playing){
            document.getElementById('cell_' + py + px).innerHTML = '<img  src="img/player.png" />';
        }
        }

        //KEYBOARD INPUTS USES KEYPRESS.JS       
        //W MOVE        
        listener.simple_combo("w", function() {
            console.log("You pressed w");
            clearPlayer();
            py = py - 1;
            if (py < 0) {
                py = 0;
            }

            setPlayerPos(px, py)
        });

        //S MOVE        
        listener.simple_combo("s", function() {
            console.log("You pressed s");
            clearPlayer();
            py = py + 1;
            if (py > map.length - 1) {
                py = map[0].length - 1;
            }

            setPlayerPos(px, py)
        });

        //A MOVE        
        listener.simple_combo("a", function() {
            console.log("You pressed a");
            clearPlayer();
            px = px - 1;
            if (px < 0) {
                px = 0;
            }

            setPlayerPos(px, py)
        });

        //D MOVE        
        listener.simple_combo("d", function() {
            console.log("You pressed d");
            clearPlayer();
            px = px + 1;
            if (px > map[0].length - 1) {
                px = map[0].length - 1;
            }

            setPlayerPos(px, py)
        });


        //CLEAR PLAYER LAST POSITION IMAGE
        function clearPlayer() {
            if (playing) {
                document.getElementById('cell_' + py + px).innerHTML = '';
            }
        }


        function drawCounter() {
            document.getElementById('showCounter').innerHTML = 'Time: ' + last / 100;
        }

        //SIMPLE GAME LOOP  
        function update(progress) {
            // Update the state of the world for the elapsed time since last render
            if (playing) {
                if (last + 100 == counter) {
                    last = counter;
                    //checkDice();
                    checkGameOver();
                }
            }
        }

        function draw() {
            // Draw the state of the world
            counter++;
            if (playing) {

                drawCounter();
                checkPlayerCol(px, py, enx, eny);

            }
        }

        function loop(timestamp) {
            var progress = timestamp - lastRender;
            update(progress);
            draw();
            lastRender = timestamp;
            window.requestAnimationFrame(loop);
        }

        //RANDOM EVENT

        //create random event
        function createRandomEvent(min, max) {
            if (playing) {
                lastRndEvt = 0;
                var rndRes = getRnd(min, max);
                nextRndEvt = counter + rndRes;
            }
        }





        //Random Number
        function getRnd(min, max) {
            min = Math.ceil(min);
            max = Math.floor(max);
            return Math.floor(Math.random() * (max - min + 1)) + min;
        }

        //Roll Dice
        function rollDice(min, max, wingt) {
            var roll = getRnd(min, max);
            if (roll > wingt) {
                return true;
            } else {
                return false;
            }
        }

        //Check Roll Outcome
        function checkDice() {
            var res = rollDice(1, 6, 4);
            if (res) {
                console.log('event')
            } else {
                console.log('no event');
            }
        }



        //Draws an enemy on the map
        function setEnemies() {
            var enemyArr = [
                ['Crab', '1', 'img/enCrab.png']
            ];
            enx = getRnd(0, map.length - 1);
            eny = getRnd(0, map[0].length - 1);
            if (checkColzn(enx, eny, px, py)) {
                setEnemies();
            } else {
                if (playing) {
                    document.getElementById('cell_' + eny + enx).innerHTML = '<img src="' + enemyArr[0][2] + '"/>';
                }
            }
        }


        //Check for collision
        function checkColzn(coldx, coldy, coldex, coldey) {
            if (coldx == coldex && coldy == coldey) {
                return true;
            } else {
                return false;
            }
        }

        //Check Player Collision
        function checkPlayerCol(coldx, coldy, coldex, coldey) {

            if (checkColzn(coldx, coldy, coldex, coldey)) {
                var snd = new Audio("catch.mp3");
                snd.play();
                destroyEnemy();
                enx = 0;
                eny = 0;
                points = points + 10;
                drawPoints();
                setEnemies();
            }

        }
        
        //FOR ADDITIONAL FUNCS
        function destroyEnemy() {}

        //updates points display
        function drawPoints() {
            document.getElementById('showPoints').innerHTML = 'Points: ' + points;
        }

        //check if game is over
        function checkGameOver() {
            var a = started + 1;
            var b = counter;
            console.log(a + '  ' + b);
                if (a + roundLngth < b) {
                    playing = false;
                    gameOver();
                }
        }

        //when game is over
        function gameOver() {
            var text = '';
            text += '<h1 class="center-text">GAME OVER</h1>';
            text += '<h2>Final Score ' + points + '</h2>';
            text += '<button onclick="newGame()">PLAY AGAIN</button>';
            document.getElementById('cont').innerHTML = text;
        }

        //when starting a new game
        function newGame() {
            points = 0;
            last = 0;
            counter = 0;
            started = 0;
            var text = '<div class="row"><div class="points" id="showPoints">Points: 0</div><div id="showCounter" class="time">Time Elapsed: 0</div></div><table class="maptable"><tr><td id="cell_00"></td><td id="cell_01"></td><td id="cell_02"></td><td id="cell_03"></td></tr><tr><td id="cell_10"></td><td id="cell_11"></td><td id="cell_12"></td><td id="cell_13"></td></tr><tr><td id="cell_20"></td><td id="cell_21"></td><td id="cell_22"></td><td id="cell_23"></td></tr><tr><td id="cell_30"></td><td id="cell_31"></td><td id="cell_32"></td><td id="cell_33"></td></tr></table>                    <div id="startBtn" class="text-center"><br/><button class="btn btn-primary btn-lg center-block" onclick="startGame()">START</button><br/><p><img src="img/enCrab.png">Baby dragon just learned to teleport!</p><p>Catch baby dragon as many times as you can before the timer reaches 20.</p><p>Use the keyboard to play. W, A, S and D are the movement keys. W is up. D is right, S down and A is left.</p></div>';

            document.getElementById('cont').innerHTML = text;
           
        }
    </script>
</body>
</html>