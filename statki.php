<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Battleship Game</title>
    <style>

 body {
            background-image: image-set("dT9rXxAkc.png");
            background-size: cover;
        }
        .tyt{
            text-align: center;
            color: darkblue;
        }
        .status{
            text-align: center;
        }
        .board  {

            display: grid;
            grid-template-columns: 40px repeat(10, 40px);
            gap: 5px;
        }

        .cell {
            width: 40px;
            height: 40px;
            border: 1px solid #000;
            text-align: center;
            line-height: 40px;
            cursor: pointer;
        }

        .coordinates {
            display: grid;
            grid-template-columns: 40px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .coordinate {
            text-align: center;
        }

        .cos{
            width: 40px;
            height: 40px;
        }
    </style>
</head>

<body>

        <a href="register.php">Register</a> <a href="logout.php">Logout</a>

        <h1 class="tyt">Statki</h1>

<h2 style="float: left;">Twoje</h2>
<div id="playerBoard" class="board" style="float: left;"></div>


<h2 style="float: right;">CPU</h2>
<div id="cpuBoard" class="board" style="float: right;"></div>


<h2 class="status">Status</h2>
<div id="gameStatus" class="status"></div>



    <script>
        const shipLengths = [4, 3, 3, 2, 2, 2, 2, 1, 1, 1, 1];
        const playerBoard = document.getElementById('playerBoard');
        const cpuBoard = document.getElementById('cpuBoard');
        const gameStatus = document.getElementById('gameStatus');
        const playerShips = [];
        const cpuShips = [];
        let playerShipsLeft = 22;
        let cpuShipsLeft = 22;

        function createBoard(boardElement, onClick) {
            const coordinatesRow = document.createElement('div');
            coordinatesRow.className = 'coordinates';

            for (let i = 0; i < 11; i++) {
                const coordinate = document.createElement('div');
                coordinate.className = 'coordinate';
                coordinate.textContent = i === 0 ? '' : String.fromCharCode(64 + i);
                coordinatesRow.appendChild(coordinate);
            }

            boardElement.appendChild(coordinatesRow);

            for (let i = 0; i < 10; i++) {
                const row = document.createElement('div');
                row.className = 'board-row';

                for (let j = 0; j < 11; j++) {
                    if (j === 0) {
                        const coordinate = document.createElement('div');
                        coordinate.className = 'cell coordinate';
                        coordinate.textContent = i + 1;
                        row.appendChild(coordinate);
                    } else {
                        const cell = document.createElement('div');
                        cell.className = 'cell';
                        cell.dataset.index = i * 10 + (j - 1);

                        if (boardElement.id === 'playerBoard') {
                            cell.addEventListener('click', () => onClick(i * 10 + (j - 1)));
                        }

                        row.appendChild(cell);
                    }
                }

                boardElement.appendChild(row);
            }
        }

        function initializeShips() {

            for (let i = 0; i < shipLengths.length; i++) {
                let shipPlaced = false;
                while (!shipPlaced) {
                    const orientation = Math.random() < 0.5 ? 'horizontal' : 'vertical';
                    const shipStart = Math.floor(Math.random() * 100);

                    if (canPlaceShip(shipStart, shipLengths[i], orientation, playerShips)) {
                        placeShip(shipStart, shipLengths[i], orientation, playerShips);
                        shipPlaced = true;
                    }
                }
            }

            for (let i = 0; i < shipLengths.length; i++) {
                let shipPlaced = false;
                while (!shipPlaced) {
                    const orientation = Math.random() < 0.5 ? 'horizontal' : 'vertical';
                    const shipStart = Math.floor(Math.random() * 100);

                    if (canPlaceShip(shipStart, shipLengths[i], orientation, cpuShips)) {
                        placeShip(shipStart, shipLengths[i], orientation, cpuShips);
                        shipPlaced = true;
                    }
                }
            }
        }

        function canPlaceShip(startIndex, length, orientation, ships) {
            const endIndex = orientation === 'horizontal' ? startIndex + length - 1 : startIndex + (length - 1) * 10;

            if (endIndex >= 100) {
                return false;

            }

            for (let i = startIndex; i <= endIndex; i++) {
                if (ships.includes(i) || hasAdjacentShip(i, orientation, ships) || hasNeighboringShip(i, length, orientation, ships)) {
                    return false;
                }
            }

            return true;
        }

        function hasNeighboringShip(index, length, orientation, ships) {
            const adjacentIndices = [];

            for (let i = 0; i < length; i++) {
                if (orientation === 'horizontal') {
                    adjacentIndices.push(index - 10 + i, index + 10 + i);
                } else {
                    adjacentIndices.push(index - 1 + i * 10, index + 1 + i * 10);
                }
            }

            return adjacentIndices.some(adjacentIndex => ships.includes(adjacentIndex));
        }


        function hasAdjacentShip(index, orientation, ships) {
            const adjacentIndices = [];

            if (index % 10 !== 0) {
                adjacentIndices.push(index - 1);
                if (Math.floor(index / 10) !== 0) {
                    adjacentIndices.push(index - 11);
                }
                if (Math.floor(index / 10) !== 9) {
                    adjacentIndices.push(index + 9);
                }
            }

            if (index % 10 !== 9) {
                adjacentIndices.push(index + 1);
                if (Math.floor(index / 10) !== 0) {
                    adjacentIndices.push(index - 9);
                }
                if (Math.floor(index / 10) !== 9) {
                    adjacentIndices.push(index + 11);
                }
            }

            if (Math.floor(index / 10) !== 0) {
                adjacentIndices.push(index - 10);
            }

            if (Math.floor(index / 10) !== 9) {
                adjacentIndices.push(index + 10);
            }

            if (orientation === 'horizontal') {
                adjacentIndices.push(index - 10, index + 10);
            } else {
                adjacentIndices.push(index - 1, index + 1);
            }

            return adjacentIndices.some(adjacentIndex => ships.includes(adjacentIndex));
        }

        function placeShip(startIndex, length, orientation, ships) {
            const shipIndices = [startIndex];

            for (let i = 1; i < length; i++) {
                shipIndices.push(orientation === 'horizontal' ? startIndex + i : startIndex + i * 10);
            }

            ships.push(...shipIndices);
        }

        function handlePlayerClick(index) {
    if (cpuShips.includes(index)) {
        alert('Hit! Player scored a point.');
        console.log('CPU Ship hit at index:', index);
        document.querySelector(`#cpuBoard .cell[data-index='${index}']`).innerHTML = '<img src="traf.png" alt="S" class="cos">';
        cpuShipsLeft--;
    } else {
        alert('Miss! Player missed.');
        console.log('Player missed at index:', index);
        document.querySelector(`#cpuBoard .cell[data-index='${index}']`).innerHTML = '<img src="kropka.png" alt="X" class="cos">';
    }
    cpuMove();
    updateGameStatus();
}

function handleCpuClick(index) {
    if (playerShips.includes(index)) {
        alert('Hit! CPU scored a point.');
        console.log('Player Ship hit at index:', index);
        document.querySelector(`#playerBoard .cell[data-index='${index}']`).innerHTML = '<img src="traf.png" alt="S" class="cos">';
        playerShipsLeft--;
    } else {
        alert('Miss! CPU missed.');
        console.log('CPU missed at index:', index);
        document.querySelector(`#playerBoard .cell[data-index='${index}']`).innerHTML = '<img src="kropka.png" alt="X" class="cos">';
    }
    updateGameStatus();
}

function cpuMove() {
    const randomIndex = Math.floor(Math.random() * 100);
    if (playerShips.includes(randomIndex)) {
        alert('CPU hit your ship!');
        console.log('Player Ship hit at index:', randomIndex);
        document.querySelector(`#playerBoard .cell[data-index='${randomIndex}']`).innerHTML = '<img src="traf.png" alt="S" class="cos">';
        playerShipsLeft--;
    } else {
        alert('CPU missed.');
        console.log('CPU missed at index:', randomIndex);
        document.querySelector(`#playerBoard .cell[data-index='${randomIndex}']`).innerHTML = '<img src="kropka.png" alt="X" class="cos">';
    }
}

function displayShips(boardElement, ships) {
    if (boardElement.id === 'playerBoard') {
        ships.forEach(index => {
            document.querySelector(`#${boardElement.id} .cell[data-index='${index}']`).innerHTML = '<img src="statek.png" alt="Q" class="cos">';
        });
    }
}


        function updateGameStatus() {
            const totalShipCells = shipLengths.reduce((sum, length) => sum + length, 0);

            if (playerShipsLeft === 0) {
                gameStatus.textContent = `Congratulations! You won! Total Ship Cells: ${totalShipCells}`;

                if (confirm('Do you want to play again?')) {
                    resetGame();
                }
            } else if (cpuShipsLeft === 0) {
                gameStatus.textContent = `CPU won. Better luck next time. Total Ship Cells: ${totalShipCells}`;

                if (confirm('Do you want to play again?')) {
                    resetGame();
                }
            } else {
                gameStatus.textContent = `Player ships left: ${playerShipsLeft} | CPU ships left: ${cpuShipsLeft}`;
            }
        }
        function resetGame() {
            playerShips.length = 0;
            cpuShips.length = 0;

            playerShipsLeft = 22;
            cpuShipsLeft = 22;

            playerBoard.innerHTML = '';
            cpuBoard.innerHTML = '';
            gameStatus.textContent = '';
            initializeShips();
            createBoard(playerBoard, handlePlayerClick);
            createBoard(cpuBoard, handleCpuClick);
            displayShips(playerBoard, playerShips);
            displayShips(cpuBoard, cpuShips);
        }

        createBoard(playerBoard, handlePlayerClick);
        createBoard(cpuBoard, handleCpuClick);
        initializeShips();
        displayShips(playerBoard, playerShips);
        displayShips(cpuBoard, cpuShips);
        updateGameStatus();
    </script>
</body>

</html>