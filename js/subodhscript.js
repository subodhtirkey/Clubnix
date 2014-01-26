<script type="text/javascript" language="javascript">

//friend adding and removal system

var thisRandNum = "<?php echo $thisRandNum; ?>";
var friendRequestURL="scripts_for_profile/request_as_friend.php";

function addasfriend(a,b)
{
	$("#add_friend_loader").show();
	$.post(friendRequestURL,{request:"requestFriendship", mem1: a, mem2: b, thisWipit: thisRandNum },
	function(data)
		{
			$("#add_friend").html(data).show().fadeOut(12000);
		});	
}



function removeasfriend(a,b)
{
	$("#remove_friend_loader").show();
	$.post(friendRequestURL,{request:"removeFriendShip",mem1:a, mem2:b,thisWipit:thisRandNum},function(data)
		{
			$("remove_friend").html(data).show().fadeOut(12000);	
		});
}


function acceptFriendRequest(x)
{
	$.post(friendRequestURL,{request:"acceptFriend", reqID:x, thisWipit:thisRandNum},function(data)
	{
		$("#req"+x).html(data).show()
		});
}

function denyFriendRequest(x)
{
	$.post(friendRequestURL,{request:"denyFriend", reqID:x, thisWipit:thisRandNum}, function(data)
	{
		$("#req"+x).html(data).show()
		});	
}

//ending friend adding and removal system
//starting private messaging stuff

$('#pmform').submit(function(){
	$('input[type=submit]',this).attr('disabled','disabled');
	});
function pmsend(){
	var pmSubject=$('#pmsubject');
	var pmTextArea=$('#pmtextarea');
	var senderName=$('#pm_sender_name');
	var senderId=$('#pm_sender_id');
	var recName=$('#pm_rec_name');
	var recId=$('#pm_rec_id');
	var pmWipit=$('#pmwipit');
	var url="scripts_for_profile/private_msg_parse";
	if(pmSubject.val()=="")
	{
		$('#interactionresults').html('Please type a subject').show().fadeOut(6000);
	}
	else if(pmTextArea.val()=="")
	{
		$('#interactionresults').html('Please type a subject').show().fadeOut(6000);
	}
	else
	{
		$.post(url,{subject:pmSubject.val(),message:pmTextArea.val(),senderName:senderName.val(),senderID:senderId.val(),rcpntName:recName.val(),rcpntID:recId.val(),thisWipit: pm_wipit.val()},
		function(data){
		$('#private_message').slideUp(fast);
		$('#interactionresults').html(data).show().fadeOut(10000);
		document.pmForm.pmTextArea.value='';
		document.pmForm.pmSubject.value='';
		});
	}

}

</script>