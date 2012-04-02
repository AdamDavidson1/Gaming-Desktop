<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <title>Gaming Tables</title>
	<link rel="stylesheet" type="text/css" media="all" href="style/style.css" />
    <script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="js/jquery-ui-1.8.18.custom.min.js"></script>
    <script type="text/javascript" src="js/swfobject/swfobject.js"></script>
    <script type="text/javascript">
		var GM = {
			isSocketReady: false,
			newMsg: function(user, msg, currentDate){
				var socket = this.socketFlash();
				socket.addChat('<a title="'+currentDate+'"><strong>'+user+':</strong> '+msg+'</a>');
			},
			getChat: function(msg){
				var scrollDown = true;

                if($('#display').scrollTop() != ($('#display')[0].scrollHeight - $('#display').height())){
                    scrollDown = false;
                }
				if(!$('#display').html()){
					$('#display').append(msg);
				}else{
					$('#display').append('<br /><br />'+msg);
				}
				if(scrollDown){
						$('#display').scrollTop($('#display')[0].scrollHeight - $('#display').height());
				}
			},
			socketReady: function(){
				this.isSocketReady = true;
				$('#chat').css('display','block');
			},
			socketFlash: function(){
				var movieName = 'socket';
				if (window.document[movieName]){
					return window.document[movieName];
				}
				if (navigator.appName.indexOf("Microsoft Internet")==-1){
					if (document.embeds && document.embeds[movieName])
						return document.embeds[movieName];
				}
				else{
					return document.getElementById(movieName);
				}
			}
		};

		$(document).ready(function(){
			$('#main').height($(window).height()-(parseFloat($('body').css('marginTop').replace('px','')) + parseFloat($('body').css('marginBottom').replace('px','')) + 2));
			$(window).resize(function(){
				$('#main').height($(window).height()-(parseFloat($('body').css('marginTop').replace('px','')) + parseFloat($('body').css('marginBottom').replace('px','')) + 2));
			});

			$('.draggable').draggable({ 
										handle:'.title', 
										cursor: 'move',
										containment: '#main'
			});
			$('#chat-input').keyup(function(e){
				if(e.keyCode == 13 || e.which == 13){
					if($(this).val()){
						GM.newMsg('Dark', $(this).val(), new Date().toString());
						$(this).val('');
					}
				}
			});
		});
		swfobject.embedSWF('swf/socket.swf', 'socket', "0","0", "11", null, {'host': '127.0.0.1', 'port':10000 },{allowScriptAccess: "sameDomain"});
	</script>
</head>
<body>
	<div id="main">
		<div id="chat" class="draggable" style="display: none">
			<div class="title">Chat</div>
			<div id="display"></div>
			<input type="text" id="chat-input" />
		</div>
	</div>
	<div id="socket"> </div>
</body>
</html>
