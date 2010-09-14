<?php
// $Id: about.php,v 1.02 2009/06/23 17:30:00 wishcraft Exp $

include 'admin_header.php';
xoops_cp_header();
?>
<img src="../images/prizese_slogo.png" alt="Prizes" style="float: left; margin: 0 10px 5px 0;" />
<h4 style="margin: 0;">Prizes</h4>
<p style="margin-top: 0;">
Version <?=number_format($xoopsModule->getVar('version')/100, 2);?><br />
Presented by <a href="http://www.chronolabs.org.au/" target="_blank">Chronolabs</a> <br />
Copyright &copy; 2009 Simon Roberts (wishcraft)
<br clear="all" />
</p>

<h4 style="margin: 0;">License</h4>
<p style="margin-top: 0;">
This software is licensed under the CC-GNU GPL.<br />
<a href="http://creativecommons.org/licenses/GPL/2.0/" target="_blank">Commons Deed</a> |
<a href="http://www.gnu.org/copyleft/gpl.html" target="_blank">Legal Code</a>
</p>

<h4 style="margin: 0;">Who to Contact</h4>
<p style="margin-top: 0;">If you have any questions, comments or bug reports, please register and post your message on the <a href="http://www.chronolabs.org.au/forums/" target="_blank">discussion area</a>.
</p>

<h4 style="margin: 0;">Help us keep going</h4>
<p style="margin: 0;">
Prizes is Freeware and Opensource. If you think it is useful and would like to show your appreciation, you can support us in one of the following ways:
</p>
<ul>
	<li><a href="https://www.paypal.com/xclick/business=sales%40chronolabs.org.au&item_name=Donation+for+Chronolabds+Freewares&item_number=prizes&no_note=1&tax=0&currency_code=AUD">Donate us via PayPal</a>
	</li>
	<li><a href="http://www.chronolabs.org.au/">Hire us for your web development projects</a>
	</li>
</ul>

<?php
xoops_cp_footer();
?>