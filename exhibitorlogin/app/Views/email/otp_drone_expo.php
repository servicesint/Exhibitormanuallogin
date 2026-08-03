<!DOCTYPE html>
<html xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office" lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Drone Expo Welcome Email</title>
	<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">
	<style>
		* {
			box-sizing: border-box;
		}

		body {
			margin: 0;
			padding: 0;
			background-color: #cbdbf5;
			-webkit-text-size-adjust: none;
			text-size-adjust: none;
		}

		a {
			text-decoration: none;
			color: inherit;
		}

		img {
			border: 0;
			height: auto;
			display: block;
		}

		@media (max-width:700px) {
			.desktop_hide {
				display: table !important;
				max-height: none !important;
			}

			.mobile_hide {
				display: none !important;
			}

			.stack .column {
				width: 100% !important;
				display: block !important;
			}

			.row-content {
				width: 100% !important;
			}

			.text-center-mobile {
				text-align: center !important;
			}

			.pad-mobile {
				padding: 10px 20px !important;
			}
		}
	</style>
</head>

<body style="margin:0; background-color:#cbdbf5;">
	<table width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation" style="background-color:#cbdbf5;">
		<tr>
			<td align="center">
				<!-- Main Container -->
				<table width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation">
					<tr>
						<td align="center" style="padding:0;">
							<!-- Top Spacer -->
							<table width="680" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width:100%; max-width:680px; margin:0 auto;">
								<tr>
									<td style="height:10px; font-size:0;">&nbsp;</td>
								</tr>
							</table>

							<!-- Logo & Header Row -->
							<table width="680" border="0" cellpadding="0" cellspacing="0" role="presentation" style="background-color:#ffffff; width:100%; max-width:680px; margin:0 auto;">
								<tr>
									<td style="padding:10px 15px 20px 15px; width:25%; vertical-align:middle;">
										<a href="https://droneexpo.in/" target="_blank">
											<img src="<?= $logo; ?>" width="98" alt="Drone Expo" style="width:100%; max-width:98px;">
										</a>
									</td>
									<td style="padding:5px 15px; width:75%; vertical-align:middle;">
										<div style="height:15px; font-size:0;">&nbsp;</div>
										<div align="right">
											<img src="<?= $sub_event_date_image; ?>" width="408" style="max-width:40%;" alt="spacer">
										</div>
									</td>
								</tr>
							</table>

							<!-- Welcome Banner -->
							<table width="680" border="0" cellpadding="0" cellspacing="0" role="presentation" style="background-color:#104b7d; background-image:url('https://d1oco4z2z1fhwp.cloudfront.net/templates/default/8011/2931-2.png'); background-repeat:no-repeat; width:100%; max-width:680px; margin:0 auto;">
								<tr>
									<td style="padding:15px 10px; text-align:center;">
										<h1 style="margin:0; color:#ffffff; font-family:'Nunito', Arial, sans-serif; font-size:20px; font-weight:700;">Welcome to Drone Expo</h1>
									</td>
								</tr>
							</table>

							<!-- Dashed separator row (empty content but keeps structure) -->
							<table width="680" border="0" cellpadding="0" cellspacing="0" role="presentation" style="background-color:#ffffff; border-left:1px dashed #C8C8C8; border-right:1px dashed #C8C8C8; border-bottom:1px dashed #C8C8C8; width:100%; max-width:680px; margin:0 auto;">
								<tr>
									<td style="padding:10px 20px;"></td>
								</tr>
							</table>

							<!-- Spacer & Divider -->
							<table width="680" border="0" cellpadding="0" cellspacing="0" role="presentation" style="background-color:#ffffff; width:100%; max-width:680px; margin:0 auto;">
								<tr>
									<td style="height:15px;font-size: 15px;padding-left: 20px;padding-right: 20px;"><?= $message; ?></td>
								</tr>
								<tr>
									<td style="padding:10px 10px 15px;">
										<table width="95%" border="0" cellpadding="0" cellspacing="0" align="center" style="border-top:1px solid #dddddd;">
											<tr>
												<td style="font-size:1px;"></td>
											</tr>
										</table>
									</td>
								</tr>
							</table>

							<!-- Contact details header -->
							<table width="680" border="0" cellpadding="0" cellspacing="0" role="presentation" style="background-color:#d3d3d3; width:100%; max-width:680px; margin:0 auto;">
								<tr>
									<td style="padding:10px; text-align:center;">
										<h1 style="margin:0; font-family:'Nunito', Arial, sans-serif; font-size:18px; font-weight:400;">Get in touch with us using the contact details below</h1>
									</td>
								</tr>
							</table>

							<!-- Thin black spacer -->
							<table width="680" border="0" cellpadding="0" cellspacing="0" role="presentation" style="background-color:#000000; width:100%; max-width:680px; margin:0 auto;">
								<tr>
									<td style="height:5px; font-size:0;">&nbsp;</td>
								</tr>
							</table>

							<table width="680" border="0" cellpadding="0" cellspacing="0" role="presentation" style="background-color:#000000; width:100%; max-width:680px; margin:0 auto;" class="desktop_hide">
								<tr>
									<!-- Left Column: App Download -->
									<td style="width:50%; vertical-align:top; padding:5px;">
										<table width="100%" border="0" cellpadding="0" cellspacing="0">
											<tr>
												<td style="padding-bottom:10px; text-align:center;">
													<h3 style="margin:0; color:#ffffff; font-family:Arial, sans-serif; font-size:18px;"><strong>Download Drone Expo App</strong></h3>
												</td>
											</tr>
											<tr>
												<td style="padding-bottom:5px; text-align:center;">
													<a href="https://play.google.com/store/apps/details?id=com.servint.droneexpo" target="_blank">
														<img src="https://www.fireindia.net/mailer/FI-1693891123.png" width="170" alt="Google Play" style="max-width:170px; width:100%;">
													</a>
												</td>
											</tr>
											<tr>
												<td style="padding-top:5px; text-align:center;">
													<a href="https://apps.apple.com/us/app/drone-expo/id6463078814" target="_blank">
														<img src="https://www.fireindia.net/mailer/FI-1693891148.png" width="170" alt="App Store" style="max-width:170px; width:100%;">
													</a>
												</td>
											</tr>
										</table>
									</td>
									<!-- Right Column: Contact -->
									<td style="width:50%; vertical-align:top; padding:5px;">
										<table width="100%" border="0" cellpadding="0" cellspacing="0">
											<tr>
												<td style="padding-bottom:10px; padding-left:20px;">
													<h3 style="margin:0; color:#ffffff; font-family:Arial, sans-serif; font-size:18px;"><strong>Contact</strong></h3>
												</td>
											</tr>
											<tr>
												<td style="padding:10px 20px 8px;">
													<p style="margin:0; font-family:'Nunito', Arial, sans-serif; font-size:14px; color:#ffffff;">
														<a href="mailto:info@droneexpo.in" style="color:#ffffff;">info@droneexpo.in</a>
													</p>
												</td>
											</tr>
											<tr>
												<td style="padding:8px 20px;">
													<p style="margin:0; font-family:'Nunito', Arial, sans-serif; font-size:14px; color:#ffffff;">
														<a href="tel:011-45055579" style="color:#ffffff;">011-45055579</a>
													</p>
												</td>
											</tr>
											<tr>
												<td style="padding:10px 20px;">
													<table border="0" cellpadding="0" cellspacing="0">
														<tr>
															<td style="padding-right:4px;"><a href="https://www.facebook.com/DroneExpo.in" target="_blank"><img src="https://app-rsrc.getbee.io/public/resources/social-networks-icon-sets/circle-color/facebook@2x.png" width="32" height="32" alt="Facebook"></a></td>
															<td style="padding-right:4px;"><a href="https://twitter.com/DroneExpo_com" target="_blank"><img src="https://app-rsrc.getbee.io/public/resources/social-networks-icon-sets/circle-color/twitter@2x.png" width="32" height="32" alt="Twitter"></a></td>
															<td style="padding-right:4px;"><a href="https://www.linkedin.com/in/drone-expo/" target="_blank"><img src="https://app-rsrc.getbee.io/public/resources/social-networks-icon-sets/circle-color/linkedin@2x.png" width="32" height="32" alt="LinkedIn"></a></td>
															<td style="padding-right:4px;"><a href="https://www.instagram.com/droneexpo.in/" target="_blank"><img src="https://app-rsrc.getbee.io/public/resources/social-networks-icon-sets/circle-color/instagram@2x.png" width="32" height="32" alt="Instagram"></a></td>
															<td style="padding-right:4px;"><a href="https://www.youtube.com/@droneexpo" target="_blank"><img src="https://app-rsrc.getbee.io/public/resources/social-networks-icon-sets/circle-color/youtube@2x.png" width="32" height="32" alt="YouTube"></a></td>
														</tr>
													</table>
												</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>

							<!-- Mobile version (visible only on mobile, simplified stack) -->
							<table width="680" border="0" cellpadding="0" cellspacing="0" role="presentation" style="background-color:#000000; width:100%; max-width:680px; margin:0 auto; display:none;" class="desktop_hide">
								<tr>
									<td style="padding:10px 20px 15px; text-align:center;">
										<h3 style="margin:0 0 10px 0; color:#ffffff; font-size:18px;"><strong>Download Drone Expo App</strong></h3>
										<div style="margin-bottom:10px;">
											<a href="https://play.google.com/store/apps/details?id=com.servint.droneexpo" target="_blank">
												<img src="https://www.fireindia.net/mailer/FI-1693891123.png" width="170" alt="Google Play" style="max-width:170px;">
											</a>
										</div>
										<div>
											<a href="https://apps.apple.com/us/app/drone-expo/id6463078814" target="_blank">
												<img src="https://www.fireindia.net/mailer/FI-1693891148.png" width="170" alt="App Store" style="max-width:170px;">
											</a>
										</div>
									</td>
								</tr>
								<tr>
									<td style="padding:5px 20px 15px; text-align:center;">
										<h3 style="margin:0 0 8px 0; color:#ffffff; font-size:18px;"><strong>Contact</strong></h3>
										<p style="margin:5px 0; color:#ffffff;"><a href="mailto:info@droneexpo.in" style="color:#ffffff;">info@droneexpo.in</a></p>
										<p style="margin:5px 0 15px; color:#ffffff;"><a href="tel:011-45055579" style="color:#ffffff;">011-45055579</a></p>
										<table align="center" border="0" cellpadding="0" cellspacing="0">
											<tr>
												<td style="padding:0 4px;"><a href="https://www.facebook.com/DroneExpo.in" target="_blank"><img src="https://app-rsrc.getbee.io/public/resources/social-networks-icon-sets/circle-color/facebook@2x.png" width="32" height="32" alt="Facebook"></a></td>
												<td style="padding:0 4px;"><a href="https://twitter.com/DroneExpo_com" target="_blank"><img src="https://app-rsrc.getbee.io/public/resources/social-networks-icon-sets/circle-color/twitter@2x.png" width="32" height="32" alt="Twitter"></a></td>
												<td style="padding:0 4px;"><a href="https://www.linkedin.com/in/drone-expo/" target="_blank"><img src="https://app-rsrc.getbee.io/public/resources/social-networks-icon-sets/circle-color/linkedin@2x.png" width="32" height="32" alt="LinkedIn"></a></td>
												<td style="padding:0 4px;"><a href="https://www.instagram.com/droneexpo.in/" target="_blank"><img src="https://app-rsrc.getbee.io/public/resources/social-networks-icon-sets/circle-color/instagram@2x.png" width="32" height="32" alt="Instagram"></a></td>
												<td style="padding:0 4px;"><a href="https://www.youtube.com/@droneexpo" target="_blank"><img src="https://app-rsrc.getbee.io/public/resources/social-networks-icon-sets/circle-color/youtube@2x.png" width="32" height="32" alt="YouTube"></a></td>
											</tr>
										</table>
									</td>
								</tr>
							</table>

							<!-- Bottom small spacer -->
							<table width="680" border="0" cellpadding="0" cellspacing="0" role="presentation" style="background-color:#000000; width:100%; max-width:680px; margin:0 auto;">
								<tr>
									<td style="height:5px; font-size:0;">&nbsp;</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</body>

</html>