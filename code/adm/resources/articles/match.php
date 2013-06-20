<?php
$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');
$lArticleId = (int) $_GET['id'];
if( !$lArticleId ){
	header('Location: ./');
	exit();
}
HtmlStartXml(1);	

echo '			
		<div id="layerbg"></div>
			
		<div class="xmlWrapper">
			<div class="menuHolder" >
				<div class="t">
				<div class="b">
				<div class="l">
				<div class="r">

					<div class="bl">
					<div class="br">
					<div class="tl">
					<div class="tr">
						<table cellspacing="0" cellpadding="5" border="0" class="gridtable">
							<tr>
								<th class="gridtools">Menu</th>
							</tr>
							<tr>
								<td>					
									<div id="menuHolder"></div>
									</div>
								</td>
							</tr>
						</table>
					</div>
					</div>
					</div>
					</div>

				</div>
				</div>
				</div>
				</div>
			</div>
			
			<div id="textModeTools">
				<div class="textHolder" id="textTableHolder">
					<div class="t">
					<div class="b">
					<div class="l">
					<div class="r">

						<div class="bl">
						<div class="br">
						<div class="tl">
						<div class="tr">
							<table cellspacing="0" cellpadding="5" border="0" class="gridtable">
								<tr>
									<th class="gridtools">Text</th>
								</tr>
								<tr>
									<td>	
										<div id="formattingMenuHolder">
										
										</div>
										<div id="textHolder">
				
											<div class="rightClickMenu" id="rightClickMenuHolder">
												<div class="t">
												<div class="b">
												<div class="l">
												<div class="r">

													<div class="bl">
													<div class="br">
													<div class="tl">
													<div class="tr">
														<div id="rightClickMenu">
												
														</div>
													
													
														
													
													</div>
													</div>
													</div>
													</div>

												</div>
												</div>
												</div>
												</div>
												
												
												
												
												
												
												
											</div>
											<div class="rightClickMenu" id="rightClickSubMenuHolder">
												<div class="t">
												<div class="b">
												<div class="l">
												<div class="r">

													<div class="bl">
													<div class="br">
													<div class="tl">
													<div class="tr">
														<div id="rightClickSubMenu">
												
														</div>
													
													
														
													
													</div>
													</div>
													</div>
													</div>

												</div>
												</div>
												</div>
												</div>
												
												
												
												
												
												
												
											</div>
										</div>
									</td>
								</tr>
							</table>
						
						
						
							
						
						</div>
						</div>
						</div>
						</div>

					</div>
					</div>
					</div>
					</div>

				
				
					
				</div>
				
				
				<div class="xmlHolder" id="xmlTableHolder" >
					<div class="t">
					<div class="b">
					<div class="l">
					<div class="r">

						<div class="bl">
						<div class="br">
						<div class="tl">
						<div class="tr">
							<table cellspacing="0" cellpadding="5" border="0" class="gridtable">
								<tr>
									<th class="gridtools">XML</th>
								</tr>
								<tr>
									<td>					
										<div id="xmlHolder">
											<div class="rightClickMenu" id="sourceRightClickMenuHolder">
												<div class="t">
												<div class="b">
												<div class="l">
												<div class="r">

													<div class="bl">
													<div class="br">
													<div class="tl">
													<div class="tr">
														<div id="sourceRightClickMenu">
												
														</div>
													
													
														
													
													</div>
													</div>
													</div>
													</div>

												</div>
												</div>
												</div>
												</div>
												
												
												
												
												
												
												
											</div>									
										</div>
									</td>
								</tr>
							</table>
						
						</div>
						</div>
						</div>
						</div>

					</div>
					</div>
					</div>
					</div>

				
				
					
				</div>
				<div class="tagHolder" id="tagNameHolder">
					<div class="t">
					<div class="b">
					<div class="l">
					<div class="r">

						<div class="bl">
						<div class="br">
						<div class="tl">
						<div class="tr">
							<table cellspacing="0" cellpadding="5" border="0" class="gridtable">
								<tr>
									<th class="gridtools">Tags</th>
								</tr>
								<tr>
									<td>					
										<div id="tagHolder"></div>
										</div>
									</td>
								</tr>
							</table>
						</div>
						</div>
						</div>
						</div>

					</div>
					</div>
					</div>
					</div>
				</div>
				<div class="unfloat"></div>
			</div>
			<div id="sourceModeTools" style="display:none">
				<div class="textHolder sourceModeTextHolder">
					<div class="t">
					<div class="b">
					<div class="l">
					<div class="r">

						<div class="bl">
						<div class="br">
						<div class="tl">
						<div class="tr">
							<table cellspacing="0" cellpadding="5" border="0" class="gridtable">
								<tr>
									<th class="gridtools">Source</th>
								</tr>
								<tr>
									<td>					
										<div id="sourceTextAreaIframeHolder">
										
											<div class="rightClickMenu" id="sourceModeRightClickMenuHolder">
												<div class="t">
												<div class="b">
												<div class="l">
												<div class="r">

													<div class="bl">
													<div class="br">
													<div class="tl">
													<div class="tr">
														<div id="sourceModeRightClickMenu">
												
														</div>
													
													
														
													
													</div>
													</div>
													</div>
													</div>

												</div>
												</div>
												</div>
												</div>
												
												
												
												
												
												
												
											</div>
										</div>
									</td>
								</tr>
							</table>
						</div>
						</div>
						</div>
						</div>

					</div>
					</div>
					</div>
					</div>
				</div>
				<div class="unfloat"></div>
						
			</div>
		</div>
		
		
		
		
		
		<div class="hiddenPopup" id="hiddenPopupHolder">
			<div class="t">
			<div class="b">
			<div class="l">
			<div class="r">

				<div class="bl">
				<div class="br">
				<div class="tl">
				<div class="tr">
					<div id="hiddenPopup">
			
					</div>
				
				
					
				
				</div>
				</div>
				</div>
				</div>

			</div>
			</div>
			</div>
			</div>
		</div>
		<div class="rightClickMenu autotagRightClickMenu" id="autotagRightClickMenuHolder">
			<div class="t">
			<div class="b">
			<div class="l">
			<div class="r">

				<div class="bl">
				<div class="br">
				<div class="tl">
				<div class="tr">
					<div id="autotagRightClickMenu">
			
					</div>
				
				
					
				
				</div>
				</div>
				</div>
				</div>

			</div>
			</div>
			</div>
			</div>
			
			
			
			
			
			
			
		</div>
		<script>
			loadDocument(' . (int) $lArticleId . ');		
		</script>
';


HtmlEndXml(1);
?>

