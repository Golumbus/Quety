<?php
session_start();
if (isset($_POST['set_user'])) {
    $_SESSION['username'] = htmlspecialchars($_POST['username']);
}
$user = $_SESSION['username'] ?? null;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Quety - Talking Stick</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: sans-serif; text-align: center; background: #f4f4f4; padding: 20px; }
        .card { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); max-width: 400px; margin: auto; }
        .speaker-box { font-size: 1.5rem; font-weight: bold; margin-bottom: 20px; padding: 20px; height: 140px; background: #cce; border-radius: 8px; }
        .btn { padding: 15px 25px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; margin: 5px; width: 97%; }
        .btn-queue { background: yellow; color: black; }
        .btn-queue.active { background: green; color: white; }
        .btn-yes { background: #4CAF50; color: white; }
        .btn-no { background: #f44336; color: white; }
        .remark-area { margin-top: 20px; border-top: 1px solid #ddd; padding-top: 10px; }
        input[type="text"] { width: 80%; padding: 10px; margin-bottom: 10px; }
		.btn-leave {
    display: inline-block;
    color: #888;
    text-decoration: none;
    font-size: 0.9rem;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    width: 90%;
    transition: background 0.2s;
}

    </style>
</head>
<body>

<?php if (!$user): ?>
    <div class="card">
        <h2>Welcome to Quety</h2>
        <p>The Talking Stick App</p>
        <form method="POST">
            <input type="text" name="username" placeholder="Enter your name" required>
            <button type="submit" name="set_user" class="btn btn-yes">Join Meeting</button>
        </form>
    </div>
<?php else: ?>
    <div class="card">
        <div id="currentSpeaker" class="speaker-box"><p>You are welcome to speak , queue up !<p></div>
        
        <p>User: <strong><?php echo $user; ?></strong></p>
        
        <button id="queueBtn" class="btn btn-queue" onclick="toggleQueue()">Queue</button>
        
        <div style="display: flex;">
            <button id="yesBtn" class="btn btn-yes" onclick="vote('yes')">Yes (0)</button>
            <button id="noBtn" class="btn btn-no" onclick="vote('no')">No (0)</button>
        </div>

        <p id="statsLine" style="font-size: 0.9rem; color: #666; margin: 10px 0;">Attending: 0 | In Queue: 0</p>

        <div class="remark-area">
            <input type="text" id="remarkInput" placeholder="Add a remark...">
            <button onclick="saveRemark()" class="btn" style="background:#ddd">Save/Clear Remark</button>
            <div id="remarkList" style="text-align: left; font-size: 0.8rem; margin-top: 10px;"></div>
        </div>
        <div style="margin-top: 30px; border-top: 1px dashed #ccc; padding-top: 15px;">
			<a href="api.php?action=leave" class="btn-leave">Leave Meeting</a>
		</div>
        <p><a href="admin.php" style="color: #999; font-size: 0.7rem;">Admin</a></p>
    </div>

    <script>
        let myState = { queued: false, votedYes: false, votedNo: false };

        async function updateUI() {
            const res = await fetch('api.php?action=status');
            const json = await res.json();
            const data = json.data;

            // Update Speaker
            document.getElementById('currentSpeaker').innerText = data.queue.length > 0 ? "Speaking: " + data.queue[0] : "You are welcome to speak , queue up !";
            
            // Update Queue Button
            const inQueue = data.queue.includes(json.myUser);
            const qBtn = document.getElementById('queueBtn');
            qBtn.classList.toggle('active', inQueue);
            qBtn.style.backgroundColor = inQueue ? "green" : "yellow";
            qBtn.innerText= inQueue? "Unqueue" : "Queue";
            qBtn.style.color = inQueue ? "white" : "black";
            myState.queued = inQueue;
			//let a=2;
			//qBtn.innerText = "GH "+a.toString();// RVDP
			/* if ( inQueue ) {
				let cnt=0; let idx=0;
				//const words = data.queue.split(" ");
				
				for (const element in data.queue) {
				  cnt++;
				  if (element==json.myUser) { idx=cnt; }
				}  
			    qBtn.innerText= "Queued "+idx.toString()+" of "+cnt.toString();
			}
             */
			 // Update Counts
            document.getElementById('yesBtn').innerText = `Yes (${data.yes})`;
            document.getElementById('noBtn').innerText = `No (${data.no})`;

            // Update Stats Line
            const attending = data.attending || 0;
            const inQueue = data.queue.length;
            document.getElementById('statsLine').innerText = `Attending: ${attending} | In Queue: ${inQueue}`;

            // Update Remarks
            let htmla = "";
            for (const [user, msg] of Object.entries(data.remarks)) 
			{
                htmla += `<div><strong>${user}:</strong> ${msg}</div>`;
			}
            document.getElementById('remarkList').innerHTML = htmla;
        }

        function toggleQueue() { fetch('api.php?action=toggleQueue'); updateUI(); }

        function vote(type) {
            // Simple toggle logic for demo
            let mode = 'add'; 
            if(type === 'yes' && myState.votedYes) { mode = 'remove'; myState.votedYes = false; }
            else if(type === 'no' && myState.votedNo) { mode = 'remove'; myState.votedNo = false; }
            else { if(type === 'yes') myState.votedYes = true; else myState.votedNo = true; }
            
            fetch(`api.php?action=vote&type=${type}&mode=${mode}`);
            updateUI();
        }

        function saveRemark() {
            const val = document.getElementById('remarkInput').value;
            const formData = new FormData();
            formData.append('remark', val);
            fetch('api.php?action=remark', { method: 'POST', body: formData });
            document.getElementById('remarkInput').value = ""; // Clear field after save
            updateUI();
        }

        setInterval(updateUI, 2000); // Poll every 2 seconds
        updateUI();
    </script>
<?php endif; ?>

</body>
</html>