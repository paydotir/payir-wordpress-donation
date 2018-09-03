<?php
defined( 'ABSPATH' ) or die(  'No script kiddies please!' );
$notes = array
(
__('Insert into posts or pages' , 'cupri')=>__("you can use [cupri] shortcode anywhere you want",'cupri'),
__('send pre selected value to selectable fields' , 'cupri')=>__("add your value to link of your custom payment page with this sample: http://yoursite.com/custom-pay/?cupri_fX=Y while X is your field number and Y is it's value , so when user open this link that field selected value filled with sent value",'cupri'),
__('send pre defined value to price field' , 'cupri')=>__("such as above just use <i>price</i> instead of X",'cupri'),
);
?>
<div class="wrap">
	<?php foreach ($notes  as $title => $note): ?>
		<h3><?php echo $title; ?></h3>
		<p sytle=""><?php echo $note; ?></p>
	<?php endforeach ?>	
</div>