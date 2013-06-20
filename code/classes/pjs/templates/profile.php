<?php

$gTemplArr = array(
	'profile.profile_content' => '
				<div class="profileInfo">
					<div class="profilePic">{_getProfilePic(photo_id, 1)}</div>
					<div class="leftCol">
						<h1>{fullname}</h1>
						<div class="dataInfo">' . getstr('pjs.affiliation') . ':</div>
						<span class="record">{affiliation}</span><br />
						<div class="newLine"></div>
						<div class="dataInfo">
							' . getstr('pjs.address') . ':
						</div>
						<span class="record">{addr_street}
							{addr_postcode}
							{addr_city}
							{country}
						</span><br />
						<div class="newLine"></div>
						<div class="dataInfo">
							' . getstr('pjs.url') . ':
						</div>
						<span class="record"><a href="http://{website}">{website}</a></span><br />
						<div class="newLine"></div>
						<div class="dataInfo">
							' . getstr('pjs.email') . ':
						</div>
						<span class="record"><a href="mailto:{uname}">{uname}</a></span>
					</div>
					
					<div class="rightCol">
					<h3>
						' . getstr('pjs.subscriptions') . ':
					</h3>
					<div class="dataInfo">
						' . getstr('pjs.email_alerts_for_products') . ':
					</div>
					{product_types}
					<div class="newLine"></div>
					<div class="dataInfo">
						' . getstr('pjs.area_of_interest') . ':
					</div>
						{_getSubjectCategories(subject_categories)}<br />
						{_getTaxonCategories(taxon_categories)}<br />
						{_getChronoCategories(chronological_categories)}<br />
						{_getGeoCategories(geographical_categories)}<br />
					<div class="newLine"></div>
					<div class="dataInfo">
						' . getstr('pjs.email_alerts_for_all_articles_from') . ':
					</div>
					{email_alerts_from_journals}
					</div>
				</div>
				<div class="clear"></div>
	',
);

?>