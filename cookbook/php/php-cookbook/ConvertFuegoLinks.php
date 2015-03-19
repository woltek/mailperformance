<?php

//Replace with your own html file
$html = "<html> Bonjour test <a href='www.google.fr'>lien</a> </html>";

//The id of the message you want to send
$actionGuid = "000ABC";

//Set your link id (I had only one link, but you can set it in the foreach if you have more links)
$linkId = "1";
//Set the hashkey we provided you
$hashKey = "01234";
//Set your own customer id
$customerId = "ABC";
//Set your own agency id
$agencyGuid = "TEST";
//Set the target id (to whom you want to send the email)
$targetGuid = "00ABC";

//Constant : url of the catcher
const CATCHER_LINK = "http://t8.mailperformance.com/";

//How to set the parameters of the tracked link
$GV1Click =  $agencyGuid . $customerId . $actionGuid . $targetGuid .'0';
$GV1Open = $agencyGuid . $customerId .'00000'. $actionGuid . $targetGuid;

$dom = new DOMDocument();
$dom->loadHTML($html);

//Get all <a></a>
$Anodes = $dom->getElementsByTagName('a');

// For each link, we set the correct syntax
foreach ($Anodes as $node) {
	if ($node->hasAttribute('href')) {
		$oldlink = $node->getAttribute('href');
		$node = $node->setAttribute('href', setNewUrl(($oldlink),$hashKey,$linkId,$GV1Click));
	}
}

//How to add the tracking image
parseHtmlToTrackOpenMail($dom,$GV1Click);
$html = $dom->saveHTML();

return $html;
echo $html;


function setNewUrl($url,$hashKey,$linkId,$GV1Click)
{
	if ($url == '#' || empty($url)) {
		return $url;
	}
	
	$mystring = $url;
	$findme   = '#';
	//Anchor management
	$pos=strpos($mystring, $findme);
	if($pos != false)
	{
		$mystring = substr($mystring,0,$pos-strlen($mystring));
		$url= $mystring;
	}
	
	return sprintf(CATCHER_LINK . 'redirectUrl?GV1=%s&linkid=%s&targetUrl=%s', $GV1Click,$linkId,$url.'&h='.md5($hashKey.urldecode(strtolower($url))));
}


function parseHtmlToTrackOpenMail($dom,$GV1Click )
{
	$Anodes = $dom->getElementsByTagName('body');
	$img = $dom->createElement("img");
	$img->setAttribute('src', sprintf(CATCHER_LINK . 'o5.aspx?GV1=%s', $GV1Click));
	$img->setAttribute('border', "0");
	$img->setAttribute('width',"0");
	$img->setAttribute('height',"0");
	 
	foreach ($Anodes as $elm) {
		$elm->appendChild($img);
	}
	 
}