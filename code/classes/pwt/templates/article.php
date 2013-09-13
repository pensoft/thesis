<?php
$gTemplArr = array (
	// Authors
	'article.single_author_preview_row' => '
		<div class="AOF-Single-Author-Holder">
			<div class="AOF-Author-Pic">{_showPicIfExistsAOF(photo_id, authors)}</div>
			<div class="AOF-Author-Details">
				<div class="AOF-Author-Name"><a class="AOF-Author-Email" target="_blank" href="mailto:{email}">{first_name} {middle_name} {last_name} <img src="i/mail.png" /></a></div>
				<div class="AOF-Author-Affiliation">{affiliation}, {city}, {country}</div>
				<div class="AOF-Author-Site"><a target="_blank" href="{website}">{website}</a></div>
				<div class="AOF-Author-more">Articles by this author in:&nbsp;
					<span class="AOF-Author-more-link"><a target="_blank" href="http://search.labs.crossref.org/?q={first_name}+{last_name}">CrossRef</a></span>&nbsp;|&nbsp;<span class="AOF-Author-more-link"><a target="_blank" href="http://www.ncbi.nlm.nih.gov/pubmed?cmd=search&term={last_name}%20{first_name}[au]&dispmax=50">PubMed</a></span>&nbsp;|&nbsp;<span class="AOF-Author-more-link"><a target="_blank" href="http://scholar.google.com/scholar?q=%22author%3A{last_name}%20author%3A{first_name}.%22">Google Scholar</a></span>
				</div>
			</div>
			<div class="P-Clear"></div>
		</div>
	',

	'article.authors_preview_head' => '
		<div class="P-Authors-Label">' . getstr('pjs.articleAuthorsLabel') . '{_plural(records)} </div>
		<div class="P-Authors-List">

	',
	'article.authors_preview_foot' => '
		</div>
	',
	'article.authors_preview_start' => '

	',
	'article.authors_preview_end' => '

	',
	'article.authors_preview_nodata' => '

	',
	'article.authors_preview_row' => '
		<div class="AOF-Single-Author-Preview" data-author-id="{usrid}">
			<div class="AOF-Author-Pic">{_showPicIfExistsAOF(photo_id, authors)}</div>
			<div class="AOF-Author-Details">
				<div class="AOF-Author-Name"><a class="AOF-Author-Email" target="_blank" href="mailto:{email}">{first_name} {middle_name} {last_name} <img src="i/mail.png" /></a><span class="AOF-Author-Corr">{is_corresponding}</span></div>
				<div class="AOF-Author-Affiliation">{affiliation}, {city}, {country}</div>
				<div class="AOF-Author-Site"><a target="_blank" href="{website}">{website}</a></div>
				<div class="AOF-Author-more">Articles by this author in:&nbsp;
					<span class="AOF-Author-more-link"><a target="_blank" href="http://search.labs.crossref.org/?q={first_name}+{last_name}">CrossRef</a></span>&nbsp;|&nbsp;<span class="AOF-Author-more-link"><a target="_blank" href="http://www.ncbi.nlm.nih.gov/pubmed?cmd=search&term={last_name}%20{first_name}[au]&dispmax=50">PubMed</a></span>&nbsp;|&nbsp;<span class="AOF-Author-more-link"><a target="_blank" href="http://scholar.google.com/scholar?q=%22author%3A{last_name}%20author%3A{first_name}.%22">Google Scholar</a></span>
				</div>
			</div>
			<div class="P-Clear"></div>
		</div>
	',

	'article.authors_se_preview_head' => '
		<div class="P-Authors-List">
	',
	'article.authors_se_preview_foot' => '
		</div>
	',
	'article.authors_se_preview_start' => '

	',
	'article.authors_se_preview_end' => '

	',
	'article.authors_se_preview_nodata' => '

	',
	'article.authors_se_preview_row' => '
		<div class="AOF-Single-Author-Preview AOF-Single-SE-Preview">
			<div class="AOF-Author-Pic">{_showPicIfExistsAOF(photo_id, authors)}</div>
			<div class="AOF-Author-Details">
				<div class="AOF-Author-Name"><a target="_blank" href="mailto:{email}">{first_name} {middle_name} {last_name} <img src="i/mail.png" /></a></div>
				<div class="AOF-Author-Affiliation">{affiliation}, {city}, {country}</div>
				<div class="AOF-Author-Site"><a target="_blank" href="{website}">{website}</a></div>
				<div class="AOF-Author-more">Articles by the editor in:&nbsp;
					<span class="AOF-Author-more-link"><a target="_blank" href="http://search.labs.crossref.org/?q={first_name}+{last_name}">CrossRef</a></span>&nbsp;|&nbsp;<span class="AOF-Author-more-link"><a target="_blank" href="http://www.ncbi.nlm.nih.gov/pubmed?cmd=search&term={last_name}%20{first_name}[au]&dispmax=50">PubMed</a></span>&nbsp;|&nbsp;<span class="AOF-Author-more-link"><a target="_blank" href="http://scholar.google.com/scholar?q=%22author%3A{last_name}%20author%3A{first_name}.%22">Google Scholar</a></span>
				</div>
			</div>
			<div class="P-Clear"></div>
		</div>
	',


	//Supporting Agencies

	'article.supp_agencies_preview_head' => '

	',

	'article.supp_agencies_preview_foot' => '

	',
	'article.supp_agencies_preview_startrs' => '
		<div class="P-Authors-Label">Supporting agencies</div>
			<div class="P-Supp-List">
	',
	'article.supp_agencies_preview_end' => '
			</div>
	',
	'article.supp_agencies_preview_nodata' => '

	',
	'article.supp_agencies_preview_row' => '
			<div class="supp_agencies"><span class="supp_a_acronym">{_render_if(acronym, ,&nbsp;-&nbsp;)}</span>{title}</div>

	',



	'article.authors_list_template' => '
		<div class="P-Authors-Whole-List">
			<div class="P-Journal-Meta">
				Biodiversity Data Journal 1: e{article_id}
			</div>
			<div class="P-Doi-Meta">
				<span class="P-Doi-Label">' . getstr('pjs.articleDoiLabel') . '</span>
				10.3897/BDJ.1.e{article_id}
			</div>
			<div class="P-Date-holder">
				<span class="P-Date-Label">' . getstr('pjs.articleDateReceivedLabel') . '</span>
				<span class="P-Date"> {create_date}</span> |
				<span class="P-Date-Label">' . getstr('pjs.articleDateApprovedLabel') . '</span>
				<span class="P-Date"> {approve_date}</span> |
				<span class="P-Date-Label">' . getstr('pjs.articleDatePublishedLabel') . '</span>
				<span class="P-Date"> {publish_date}</span>
			</div>


			{authors}
			<div class="P-SE-Label">' . getstr('pjs.articleSELabel') . ' </div>
			{se}

			{sup_a}


			<div class="copyrights">
			© 2013. This is an open access article distributed under the terms of the <a border="0" target="_blank" href="http://creativecommons.org/licenses/by/3.0/" rel="license">Creative Commons Attribution 3.0 (CC-BY)</a>,
			which permits unrestricted use, distribution, and reproduction in any medium, provided the original author and source are credited.
			</div>
		</div>
	',

	//Citation
	'article.citations_authors_preview_head' => '',
	'article.citations_authors_preview_foot' => '',
	'article.citations_authors_preview_start' => '',
	'article.citations_authors_preview_end' => '',
	'article.citations_authors_preview_nodata' => '',
	'article.citations_authors_preview_row' => '{last_name} {_GetAuthorFirstNameFirstLetter(first_name)}{_displayCitationsAuthorSeparator(records, rownum)}
	<span class="formatAuthor">["{first_name}", "{last_name}"]{_displayCitationsAuthorSeparator(records, rownum)}</span>

	',

	'article.citation' => '
			<div class="P-Citation">

				<div class="P-Citation-Content">
					{author_names} ({pubyear}) {_GetArticleTitleForCitation(article_title)}
					Biodiversity Data Journal 1: e{article_id}.
					DOI: <a href="http://dx.doi.org/10.3897/BDJ.1.e{article_id}" target="_blank">10.3897/BDJ.1.e{article_id}</a>
				</div>

				<div id="style-choser">
			<div id="format-head">Format via ReFinder</div>
			<select class="chosen-select" id="chosen-select" onchange="callFormattingService()" data-placeholder="-- select a citation style --">
				<option selected="selected" disabled="disabled" style="display: none">-- select a citation style --</option>
				<option>Academy of management review</option>
				<option>Acm sigchi proceedings</option>
				<option>Acm siggraph</option>
				<option>Acm sig proceedings</option>
				<option>Acm sig proceedings long author list</option>
				<option>Acs chemical biology</option>
				<option>Acs nano</option>
				<option>Acta materialia</option>
				<option>Acta naturae</option>
				<option>Acta neurochirurgica</option>
				<option>Acta ophthalmologica</option>
				<option>Acta palaeontologica polonica</option>
				<option>Acta pharmaceutica</option>
				<option>Acta polytechnica</option>
				<option>Acta societatis botanicorum poloniae</option>
				<option>Acta universitatis agriculturae sueciae</option>
				<option>Administrative science quarterly</option>
				<option>Advanced engineering materials</option>
				<option>Advanced functional materials</option>
				<option>Advanced materials</option>
				<option>Advances in complex systems</option>
				<option>African zoology</option>
				<option>Aging cell</option>
				<option>Aids</option>
				<option>Allergy</option>
				<option>Alternatives to animal experimentation</option>
				<option>American anthropological association</option>
				<option>American association for cancer research</option>
				<option>American association of petroleum geologists</option>
				<option>American chemical society</option>
				<option>American chemical society with titles</option>
				<option>American chemical society with titles brackets</option>
				<option>American geophysical union</option>
				<option>American heart association</option>
				<option>American institute of aeronautics and astronautics</option>
				<option>American institute of physics</option>
				<option>American journal of agricultural economics</option>
				<option>American journal of archaeology</option>
				<option>American journal of botany</option>
				<option>American journal of epidemiology</option>
				<option>American journal of human genetics</option>
				<option>American journal of medical genetics</option>
				<option>American journal of neuroradiology</option>
				<option>American journal of orthodontics and dentofacial orthopedics</option>
				<option>American journal of physical anthropology</option>
				<option>American journal of political science</option>
				<option>American journal of respiratory and critical care medicine</option>
				<option>American medical association</option>
				<option>American medical association alphabetical</option>
				<option>American medical association no et al</option>
				<option>American medical association no url</option>
				<option>American meteorological society</option>
				<option>American physics society</option>
				<option>American physiological society</option>
				<option>American phytopathological society</option>
				<option>American phytopathological society numeric</option>
				<option>American political science association</option>
				<option>American society for microbiology</option>
				<option>American society of civil engineers</option>
				<option>American society of mechanical engineers</option>
				<option>American sociological association</option>
				<option>American veterinary medical association</option>
				<option>Analytica chimica acta</option>
				<option>Anesthesia and analgesia</option>
				<option>Anesthesiology</option>
				<option>Angewandte chemie</option>
				<option>Animal behaviour</option>
				<option>Annalen des naturhistorischen museums in wien</option>
				<option>Annales</option>
				<option>Annals of biomedical engineering</option>
				<option>Annals of botany</option>
				<option>Annals of neurology</option>
				<option>Annals of oncology</option>
				<option>Annals of the association of american geographers</option>
				<option>Annual review of astronomy and astrophysics</option>
				<option>Annual review of medicine</option>
				<option>Annual review of nuclear and particle science</option>
				<option>Annual reviews</option>
				<option>Annual reviews alphabetical</option>
				<option>Annual reviews author date</option>
				<option>Annual reviews without titles</option>
				<option>Antarctic science</option>
				<option>Apa</option>
				<option>Apa 5th edition</option>
				<option>Apa annotated bibliography</option>
				<option>Apa cv</option>
				<option>Apa no doi no issue</option>
				<option>Apa tr</option>
				<option>Applied spectroscopy</option>
				<option>Aquatic conservation</option>
				<option>Aquatic living resources</option>
				<option>Archives of physical medicine and rehabilitation</option>
				<option>Arthritis and rheumatism</option>
				<option>Arzneimitteltherapie</option>
				<option>Asa cssa sssa</option>
				<option>Asian studies review</option>
				<option>Associacao brasileira de normas tecnicas</option>
				<option>Associacao brasileira de normas tecnicas ipea</option>
				<option>Associacao brasileira de normas tecnicas note</option>
				<option>Associacao brasileira de normas tecnicas ufmg face full</option>
				<option>Associacao brasileira de normas tecnicas ufmg face initials</option>
				<option>Associacao brasileira de normas tecnicas ufpr</option>
				<option>Associacao nacional de pesquisa e ensino em transportes</option>
				<option>Association for computing machinery</option>
				<option>Ausonius editions</option>
				<option>Austral ecology</option>
				<option>Australian guide to legal citation</option>
				<option>Australian journal of earth sciences</option>
				<option>Australian journal of grape and wine research</option>
				<option>Austrian legal</option>
				<option>Avian diseases</option>
				<option>Avian pathology</option>
				<option>Aviation space and environmental medicine</option>
				<option>Basic and applied ecology</option>
				<option>Bibtex</option>
				<option>Biochemical journal</option>
				<option>Biochemistry</option>
				<option>Biochimica et biophysica acta</option>
				<option>Bioconjugate chemistry</option>
				<option>Bioelectromagnetics</option>
				<option>Bioessays</option>
				<option>Bioinformatics</option>
				<option>Biological journal of the linnean society</option>
				<option>Biological psychiatry</option>
				<option>Biological reviews</option>
				<option>Biomed central</option>
				<option>Bioorganic and medicinal chemistry letters</option>
				<option>Biophysical journal</option>
				<option>Bioresource technology</option>
				<option>Biotechniques</option>
				<option>Biotechnology advances</option>
				<option>Biotechnology and bioengineering</option>
				<option>Biotropica</option>
				<option>Blood</option>
				<option>Bluebook2</option>
				<option>Bluebook inline</option>
				<option>Bluebook law review</option>
				<option>Bmc bioinformatics</option>
				<option>Bmj</option>
				<option>Body and society</option>
				<option>Bone</option>
				<option>Bone marrow transplantation</option>
				<option>Boreal environment research</option>
				<option>Brain</option>
				<option>Brazilian journal of botany</option>
				<option>Briefings in bioinformatics</option>
				<option>British ecological society</option>
				<option>British journal of anaesthesia</option>
				<option>British journal of cancer</option>
				<option>British journal of haematology</option>
				<option>British journal of industrial relations</option>
				<option>British journal of pharmacology</option>
				<option>British journal of political science</option>
				<option>Building structure</option>
				<option>Bulletin de la societe prehistorique francaise</option>
				<option>Bulletin of marine science</option>
				<option>Byzantina symmeikta</option>
				<option>Canadian journal of dietetic practice and research</option>
				<option>Canadian journal of fisheries and aquatic sciences</option>
				<option>Catholic biblical association</option>
				<option>Cell</option>
				<option>Cell calcium</option>
				<option>Cell numeric</option>
				<option>Cell research</option>
				<option>Cell transplantation</option>
				<option>Cellular and molecular bioengineering</option>
				<option>Cellular reprogramming</option>
				<option>Centaurus</option>
				<option>Cerebral cortex</option>
				<option>Chemical research in toxicology</option>
				<option>Chemical reviews</option>
				<option>Chemical senses</option>
				<option>Chest</option>
				<option>Chicago annotated bibliography</option>
				<option>Chicago author date</option>
				<option>Chicago author date basque</option>
				<option>Chicago author date de</option>
				<option>Chicago figures</option>
				<option>Chicago fullnote bibliography</option>
				<option>Chicago fullnote bibliography no ibid</option>
				<option>Chicago library list</option>
				<option>Chicago note bibliography</option>
				<option>Chicago note biblio no ibid</option>
				<option>Chinese gb7714 1987 numeric</option>
				<option>Chinese gb7714 2005 numeric</option>
				<option>Circulation</option>
				<option>Cities</option>
				<option>Clinical cancer research</option>
				<option>Clinical infectious diseases</option>
				<option>Clinical neurophysiology</option>
				<option>Clinical orthopaedics and related research</option>
				<option>Clinical otolaryngology</option>
				<option>Clinical pharmacology and therapeutics</option>
				<option>Clio medica</option>
				<option>Cns and neurological disorders drug targets</option>
				<option>Cold spring harbor laboratory press</option>
				<option>Comision economica para america latina y el caribe</option>
				<option>Conservation biology</option>
				<option>Conservation letters</option>
				<option>Copernicus publications</option>
				<option>Coral reefs</option>
				<option>Cortex</option>
				<option>Council of science editors</option>
				<option>Council of science editors author date</option>
				<option>Critical care medicine</option>
				<option>Cuadernos de filologia clasica</option>
				<option>Culture medicine and psychiatry</option>
				<option>Current opinion</option>
				<option>Current protocols</option>
				<option>Currents in biblical research</option>
				<option>Cytometry</option>
				<option>De buck</option>
				<option>Decision sciences</option>
				<option>Dendrochronologia</option>
				<option>Deutsche gesellschaft fur psychologie</option>
				<option>Digestive and liver disease</option>
				<option>Din 1505 2</option>
				<option>Din 1505 2 alphanumeric</option>
				<option>Din 1505 2 numeric</option>
				<option>Din 1505 2 numeric alphabetical</option>
				<option>Diplo</option>
				<option>Disability and rehabilitation</option>
				<option>Drug development research</option>
				<option>Drugs of today</option>
				<option>Ear and hearing</option>
				<option>Early medieval europe</option>
				<option>Earth surface processes and landforms</option>
				<option>Ecological entomology</option>
				<option>Ecology</option>
				<option>Ecology letters</option>
				<option>Economic commission for latin america and the caribbean</option>
				<option>Economie et statistique</option>
				<option>Ecoscience</option>
				<option>Ecosystems</option>
				<option>El profesional de la informacion</option>
				<option>Elsevier harvard</option>
				<option>Elsevier harvard2</option>
				<option>Elsevier harvard without titles</option>
				<option>Elsevier vancouver</option>
				<option>Elsevier without titles</option>
				<option>Elsevier with titles</option>
				<option>Elsevier with titles alphabetical</option>
				<option>Embo reports</option>
				<option>Emerald harvard</option>
				<option>Emu austral ornithology</option>
				<option>Energy policy</option>
				<option>Entomologia experimentalis et applicata</option>
				<option>Entomological society of america</option>
				<option>Environmental and engineering geoscience</option>
				<option>Environmental and experimental botany</option>
				<option>Environmental conservation</option>
				<option>Environmental health perspectives</option>
				<option>Environmental microbiology</option>
				<option>Environmental toxicology and chemistry</option>
				<option>Environment and planning</option>
				<option>Epidemiologie et sante animale</option>
				<option>Equine veterinary education</option>
				<option>Ergoscience</option>
				<option>Ethics book reviews</option>
				<option>Ethnobiology and conservation</option>
				<option>European cells and materials</option>
				<option>European journal of clinical microbiology and infectious diseases</option>
				<option>European journal of emergency medicine</option>
				<option>European journal of immunology</option>
				<option>European journal of information systems</option>
				<option>European journal of neuroscience</option>
				<option>European journal of ophthalmology</option>
				<option>European journal of radiology</option>
				<option>European journal of soil science</option>
				<option>European respiratory journal</option>
				<option>European retail research</option>
				<option>European society of cardiology</option>
				<option>European union interinstitutional style guide</option>
				<option>Evolution</option>
				<option>Evolution and development</option>
				<option>Evolutionary anthropology</option>
				<option>Experimental eye research</option>
				<option>Eye</option>
				<option>Fachhochschule vorarlberg</option>
				<option>Federation of european microbiological societies</option>
				<option>Fertility and sterility</option>
				<option>First monday</option>
				<option>Fish and fisheries</option>
				<option>Flavour and fragrance journal</option>
				<option>Foerster geisteswissenschaft</option>
				<option>Fold and r</option>
				<option>Free radical biology and medicine</option>
				<option>French1</option>
				<option>French2</option>
				<option>French3</option>
				<option>French4</option>
				<option>French politics</option>
				<option>Freshwater biology</option>
				<option>Frontiers</option>
				<option>Frontiers in optics</option>
				<option>Fungal ecology</option>
				<option>Future science group</option>
				<option>G3</option>
				<option>Gallia</option>
				<option>Gastroenterology</option>
				<option>Geistes und kulturwissenschaften teilmann</option>
				<option>Geneses</option>
				<option>Genetics</option>
				<option>Genome biology and evolution</option>
				<option>Geoarchaeology</option>
				<option>Geochimica et cosmochimica acta</option>
				<option>Geoderma</option>
				<option>Geografie sbornik cgs</option>
				<option>Geological magazine</option>
				<option>Geology</option>
				<option>Geopolitics</option>
				<option>Georg august universitat gottingen institut fur ethnologie und ethnologische sammlung</option>
				<option>Global change biology</option>
				<option>Global ecology and biogeography</option>
				<option>Gost r 7 0 5 2008</option>
				<option>Gost r 7 0 5 2008 numeric</option>
				<option>Hamburg school of food science</option>
				<option>Hand</option>
				<option>Harvard1</option>
				<option>Harvard7de</option>
				<option>Harvard anglia ruskin university</option>
				<option>Harvard cardiff university</option>
				<option>Harvard coventry university</option>
				<option>Harvard durham university business school</option>
				<option>Harvard european archaeology</option>
				<option>Harvard gesellschaft fur bildung und forschung in europa</option>
				<option>Harvard imperial college london</option>
				<option>Harvard institut fur praxisforschung de</option>
				<option>Harvard kings college london</option>
				<option>Harvard leeds metropolitan university</option>
				<option>Harvard limerick</option>
				<option>Harvard manchester business school</option>
				<option>Harvard north west university</option>
				<option>Harvard oxford brookes university</option>
				<option>Harvard oxford brookes university faculty of health and life sciences</option>
				<option>Harvard staffordshire university</option>
				<option>Harvard swinburne university of technology</option>
				<option>Harvard the university of melbourne</option>
				<option>Harvard the university of northampton</option>
				<option>Harvard the university of sheffield school of east asian studies</option>
				<option>Harvard the university of sheffield town and regional planning</option>
				<option>Harvard university of abertay dundee</option>
				<option>Harvard university of birmingham</option>
				<option>Harvard university of gloucestershire</option>
				<option>Harvard university of greenwich</option>
				<option>Harvard university of leeds</option>
				<option>Harvard university of sunderland</option>
				<option>Harvard university of the west of england</option>
				<option>Harvard university of west london</option>
				<option>Harvard university of wolverhampton</option>
				<option>Hawaii international conference on system sciences proceedings</option>
				<option>Health services research</option>
				<option>Heart rhythm</option>
				<option>Hepatology</option>
				<option>Heredity</option>
				<option>Histoire at politique</option>
				<option>Histoire et mesure</option>
				<option>History and theory</option>
				<option>History of the human sciences</option>
				<option>Hochschule fur wirtschaft und recht berlin</option>
				<option>Hong kong journal of radiology</option>
				<option>Human mutation</option>
				<option>Human reproduction</option>
				<option>Human reproduction update</option>
				<option>Human resource management journal</option>
				<option>Hydrobiologia</option>
				<option>Hydrological sciences journal</option>
				<option>Hypotheses in the life sciences</option>
				<option>Ices journal of marine science</option>
				<option>Ieee</option>
				<option>Ieee with url</option>
				<option>Iica catie</option>
				<option>Immunological reviews</option>
				<option>Inflammatory bowel diseases</option>
				<option>Infoclio de</option>
				<option>Infoclio fr nocaps</option>
				<option>Infoclio fr smallcaps</option>
				<option>Information systems research</option>
				<option>Insectes sociaux</option>
				<option>Institute of physics harvard</option>
				<option>Institute of physics numeric</option>
				<option>International journal of audiology</option>
				<option>International journal of cancer</option>
				<option>International journal of epidemiology</option>
				<option>International journal of exercise science</option>
				<option>International journal of humanoid robotics</option>
				<option>International journal of lexicography</option>
				<option>International journal of occupational medicine and environmental health</option>
				<option>International journal of production economics</option>
				<option>International journal of radiation oncology biology physics</option>
				<option>International journal of solids and structures</option>
				<option>International journal of sports medicine</option>
				<option>International journal of wildland fire</option>
				<option>International labour organization</option>
				<option>International microbiology</option>
				<option>International organization</option>
				<option>International pig veterinary society congress proceedings</option>
				<option>International studies association</option>
				<option>International union of crystallography</option>
				<option>Inter research science center</option>
				<option>Inter ro</option>
				<option>Investigative radiology</option>
				<option>Invisu</option>
				<option>Irish historical studies</option>
				<option>Iso690 author date cs</option>
				<option>Iso690 author date en</option>
				<option>Iso690 author date fr</option>
				<option>Iso690 author date fr no abstract</option>
				<option>Iso690 full note sk</option>
				<option>Iso690 note cs</option>
				<option>Iso690 numeric brackets cs</option>
				<option>Iso690 numeric cs</option>
				<option>Iso690 numeric en</option>
				<option>Iso690 numeric fr</option>
				<option>Iso690 numeric lt</option>
				<option>Iso690 numeric sk</option>
				<option>Jahrbuch fur evangelikale theologie</option>
				<option>Javnost the public</option>
				<option>Journalistica</option>
				<option>Journal of alzheimers disease</option>
				<option>Journal of animal physiology and animal nutrition</option>
				<option>Journal of antimicrobial chemotherapy</option>
				<option>Journal of applied animal science</option>
				<option>Journal of applied ecology</option>
				<option>Journal of applied philosophy</option>
				<option>Journal of archaeological research</option>
				<option>Journal of atrial fibrillation</option>
				<option>Journal of basic microbiology</option>
				<option>Journal of biogeography</option>
				<option>Journal of biological chemistry</option>
				<option>Journal of biomedical materials research part a</option>
				<option>Journal of bone and mineral research</option>
				<option>Journal of chemical ecology</option>
				<option>Journal of chemistry and chemical engineering</option>
				<option>Journal of clinical oncology</option>
				<option>Journal of combinatorics</option>
				<option>Journal of computational chemistry</option>
				<option>Journal of dental research</option>
				<option>Journal of elections public opinion and parties</option>
				<option>Journal of evolutionary biology</option>
				<option>Journal of experimental botany</option>
				<option>Journal of field ornithology</option>
				<option>Journal of finance</option>
				<option>Journal of financial economics</option>
				<option>Journal of fish diseases</option>
				<option>Journal of food protection</option>
				<option>Journal of forensic sciences</option>
				<option>Journal of health economics</option>
				<option>Journal of hearing science</option>
				<option>Journal of hepatology</option>
				<option>Journal of hypertension</option>
				<option>Journal of industrial ecology</option>
				<option>Journal of infectious diseases</option>
				<option>Journal of information technology</option>
				<option>Journal of integrated omics</option>
				<option>Journal of investigative dermatology</option>
				<option>Journal of lipid research</option>
				<option>Journal of mammalogy</option>
				<option>Journal of management</option>
				<option>Journal of management information systems</option>
				<option>Journal of marketing</option>
				<option>Journal of medical genetics</option>
				<option>Journal of medical internet research</option>
				<option>Journal of molecular biology</option>
				<option>Journal of molecular endocrinology</option>
				<option>Journal of morphology</option>
				<option>Journal of neurophysiology</option>
				<option>Journal of neurosurgery</option>
				<option>Journal of neurotrauma</option>
				<option>Journal of oral and maxillofacial surgery</option>
				<option>Journal of orthopaedic research</option>
				<option>Journal of orthopaedic trauma</option>
				<option>Journal of paleontology</option>
				<option>Journal of perinatal medicine</option>
				<option>Journal of petrology</option>
				<option>Journal of pollination ecology</option>
				<option>Journal of pragmatics</option>
				<option>Journal of psychiatric and mental health nursing</option>
				<option>Journal of psychiatry and neuroscience</option>
				<option>Journal of roman archaeology a</option>
				<option>Journal of roman archaeology b</option>
				<option>Journal of separation science</option>
				<option>Journal of shoulder and elbow surgery</option>
				<option>Journal of simulation</option>
				<option>Journal of social archaeology</option>
				<option>Journal of spinal disorders and techniques</option>
				<option>Journal of studies on alcohol and drugs</option>
				<option>Journal of the academy of nutrition and dietetics</option>
				<option>Journal of the air and waste management association</option>
				<option>Journal of the american academy of orthopaedic surgeons</option>
				<option>Journal of the american association of laboratory animal science</option>
				<option>Journal of the american college of cardiology</option>
				<option>Journal of the american society of brewing chemists</option>
				<option>Journal of the american society of nephrology</option>
				<option>Journal of the american water resources association</option>
				<option>Journal of the brazilian chemical society</option>
				<option>Journal of the electrochemical society</option>
				<option>Journal of the royal anthropological institute</option>
				<option>Journal of thrombosis and haemostasis</option>
				<option>Journal of tropical ecology</option>
				<option>Journal of vegetation science</option>
				<option>Journal of vertebrate paleontology</option>
				<option>Journal of visualized experiments</option>
				<option>Journal of wildlife diseases</option>
				<option>Journal of zoology</option>
				<option>Juristische zitierweise</option>
				<option>Karger journals</option>
				<option>Karger journals author date</option>
				<option>Kidney international</option>
				<option>Kindheit und entwicklung</option>
				<option>Knee surgery sports traumatology arthroscopy</option>
				<option>Kolner zeitschrift fur soziologie und sozialpsychologie</option>
				<option>Korean journal of anesthesiology</option>
				<option>Kritische ausgabe</option>
				<option>Kth royal institute of technology school of computer science and communication</option>
				<option>Kth royal institute of technology school of computer science and communication sv</option>
				<option>Landes bioscience journals</option>
				<option>Language</option>
				<option>Language in society</option>
				<option>Le mouvement social</option>
				<option>Les journees de la recherche avicole</option>
				<option>Les journees de la recherche porcine</option>
				<option>Lethaia</option>
				<option>Lettres et sciences humaines fr</option>
				<option>Leviathan</option>
				<option>Limnology and oceanography</option>
				<option>Liver international</option>
				<option>Livestock science</option>
				<option>Macromolecular reaction engineering</option>
				<option>Magnetic resonance in medicine</option>
				<option>Mammal review</option>
				<option>Manchester university press</option>
				<option>Marine policy</option>
				<option>Mcgill guide v7</option>
				<option>Mcrj7</option>
				<option>Medecine sciences</option>
				<option>Media culture and society</option>
				<option>Medical history</option>
				<option>Medical physics</option>
				<option>Medicine and science in sports and exercise</option>
				<option>Melbourne school of theology</option>
				<option>Memorias do instituto oswaldo cruz</option>
				<option>Metallurgical and materials transactions</option>
				<option>Meteoritics and planetary science</option>
				<option>Methods in ecology and evolution</option>
				<option>Methods of information in medicine</option>
				<option>Metropolitiques</option>
				<option>Microbial drug resistance</option>
				<option>Microscopy and microanalysis</option>
				<option>Mis quarterly</option>
				<option>Modern humanities research association</option>
				<option>Modern humanities research association author date</option>
				<option>Modern language association</option>
				<option>Modern language association 6th edition note</option>
				<option>Modern language association underline</option>
				<option>Modern language association with url</option>
				<option>Mohr siebeck recht</option>
				<option>Molecular and biochemical parasitology</option>
				<option>Molecular and cellular proteomics</option>
				<option>Molecular biology and evolution</option>
				<option>Molecular biology of the cell</option>
				<option>Molecular ecology</option>
				<option>Molecular microbiology</option>
				<option>Molecular phylogenetics and evolution</option>
				<option>Molecular plant</option>
				<option>Molecular plant microbe interactions</option>
				<option>Molecular psychiatry</option>
				<option>Molecular psychiatry letters</option>
				<option>Molecular therapy</option>
				<option>Moore theological college</option>
				<option>Moorlands college</option>
				<option>Multidisciplinary digital publishing institute</option>
				<option>Multiple sclerosis journal</option>
				<option>Myrmecological news</option>
				<option>Nano biomedicine and engineering</option>
				<option>National archives of australia</option>
				<option>National library of medicine grant proposals</option>
				<option>National science foundation grant proposals</option>
				<option>Nature</option>
				<option>Nature neuroscience brief communications</option>
				<option>Nature no superscript</option>
				<option>Natureza e conservacao</option>
				<option>Navigation</option>
				<option>Neurology</option>
				<option>Neurology india</option>
				<option>Neuropsychologia</option>
				<option>Neuropsychopharmacology</option>
				<option>Neurorehabilitation and neural repair</option>
				<option>Neuroreport</option>
				<option>New phytologist</option>
				<option>New solutions</option>
				<option>New zealand plant protection</option>
				<option>New zealand veterinary journal</option>
				<option>Norma portuguesa 405</option>
				<option>Northeastern naturalist</option>
				<option>Nucleic acids research</option>
				<option>Obesity</option>
				<option>Occupational medicine</option>
				<option>Oikos</option>
				<option>Oncogene</option>
				<option>Ophthalmology</option>
				<option>Oral oncology</option>
				<option>Organic geochemistry</option>
				<option>Organization</option>
				<option>Organization science</option>
				<option>Ornitologia neotropical</option>
				<option>Oryx</option>
				<option>Oscola</option>
				<option>Oscola no ibid</option>
				<option>Osterreichische zeitschrift fur politikwissenschaft</option>
				<option>Owbarth verlag</option>
				<option>Oxford art journal</option>
				<option>Oxford centre for mission studies harvard</option>
				<option>Oxford studies on the roman economy</option>
				<option>Oxford the university of new south wales</option>
				<option>Padagogische hochschule heidelberg</option>
				<option>Pain</option>
				<option>Palaeontologia electronica</option>
				<option>Palaeontology</option>
				<option>Palaios</option>
				<option>Paleobiology</option>
				<option>Pediatric anesthesia</option>
				<option>Pediatric blood and cancer</option>
				<option>Pediatric research</option>
				<option>Permafrost and periglacial processes</option>
				<option>Philosophia scientiae</option>
				<option>Phyllomedusa</option>
				<option>Physiological and biochemical zoology</option>
				<option>Pisa university press</option>
				<option>Plant biology</option>
				<option>Plant physiology</option>
				<option>Plos</option>
				<option>Pm and r</option>
				<option>Pnas</option>
				<option>Polish legal</option>
				<option>Political studies</option>
				<option>Politische vierteljahresschrift</option>
				<option>Pontifical athenaeum regina apostolorum</option>
				<option>Pontifical biblical institute</option>
				<option>Poultry science</option>
				<option>Presses universitaires de rennes</option>
				<option>Proceedings of the royal society b</option>
				<option>Progress in retinal and eye research</option>
				<option>Proinflow</option>
				<option>Protein science</option>
				<option>Proteomics</option>
				<option>Psychiatry and clinical neurosciences</option>
				<option>Psychological medicine</option>
				<option>Public health nutrition</option>
				<option>Quaderni degli avogadro colloquia</option>
				<option>Quaternary research</option>
				<option>Radiographics</option>
				<option>Radiopaedia</option>
				<option>Research policy</option>
				<option>Resources conservation and recycling</option>
				<option>Revista argentina de antropologia biologica</option>
				<option>Revista de biologia tropical</option>
				<option>Revue archeologique</option>
				<option>Revue de medecine veterinaire</option>
				<option>Revue dhistoire moderne et contemporaine</option>
				<option>Rofo</option>
				<option>Romanian humanities</option>
				<option>Rose school</option>
				<option>Royal society of chemistry</option>
				<option>Rtf scan</option>
				<option>Sage harvard</option>
				<option>Sage vancouver</option>
				<option>Scandinavian journal of infectious diseases</option>
				<option>Scandinavian journal of work environment and health</option>
				<option>Scandinavian political studies</option>
				<option>Science</option>
				<option>Science of the total environment</option>
				<option>Science translational medicine</option>
				<option>Science without titles</option>
				<option>Seminars in pediatric neurology</option>
				<option>Sexual development</option>
				<option>Small</option>
				<option>Social science and medicine</option>
				<option>Social studies of science</option>
				<option>Sociedade brasileira de computacao</option>
				<option>Societe nationale des groupements techniques veterinaires</option>
				<option>Society for american archaeology</option>
				<option>Society for general microbiology</option>
				<option>Society for historical archaeology</option>
				<option>Society of biblical literature fullnote bibliography</option>
				<option>Socio economic review</option>
				<option>Soil biology and biochemistry</option>
				<option>Soziale welt</option>
				<option>Sozialpadagogisches institut berlin walter may</option>
				<option>Sozialwissenschaften teilmann</option>
				<option>Soziologie</option>
				<option>Spanish legal</option>
				<option>Spie bios</option>
				<option>Spie journals</option>
				<option>Spip cite</option>
				<option>Springer basic author date</option>
				<option>Springer basic author date no et al</option>
				<option>Springer basic brackets</option>
				<option>Springer basic brackets no et al</option>
				<option>Springer humanities author date</option>
				<option>Springer humanities brackets</option>
				<option>Springer lecture notes in computer science</option>
				<option>Springer lecture notes in computer science alphabetical</option>
				<option>Springer mathphys author date</option>
				<option>Springer mathphys brackets</option>
				<option>Springer physics author date</option>
				<option>Springer physics brackets</option>
				<option>Springerprotocols</option>
				<option>Springer socpsych author date</option>
				<option>Springer socpsych brackets</option>
				<option>Springer vancouver</option>
				<option>Springer vancouver author date</option>
				<option>Springer vancouver brackets</option>
				<option>Standards in genomic sciences</option>
				<option>Stavebni obzor</option>
				<option>Stem cells</option>
				<option>Stem cells and development</option>
				<option>St patricks college</option>
				<option>Strahlentherapie und onkologie</option>
				<option>Strategic management journal</option>
				<option>Stroke</option>
				<option>Studii teologice</option>
				<option>Stuttgart media university</option>
				<option>Surgical neurology international</option>
				<option>Swedish legal</option>
				<option>Systematic biology</option>
				<option>Taylor and francis chicago f</option>
				<option>Taylor and francis council of science editors author date</option>
				<option>Taylor and francis harvard x</option>
				<option>Taylor and francis national library of medicine</option>
				<option>Technische universitat munchen controlling</option>
				<option>Technische universitat wien</option>
				<option>Teologia catalunya</option>
				<option>Terra nova</option>
				<option>Tgm wien diplom</option>
				<option>The accounting review</option>
				<option>The american journal of cardiology</option>
				<option>The american journal of gastroenterology</option>
				<option>The american journal of geriatric pharmacotherapy</option>
				<option>The american journal of pathology</option>
				<option>The american journal of psychiatry</option>
				<option>The american naturalist</option>
				<option>The astrophysical journal</option>
				<option>The auk</option>
				<option>The bone and joint journal</option>
				<option>The british journal of psychiatry</option>
				<option>The british journal of sociology</option>
				<option>The company of biologists</option>
				<option>The condor</option>
				<option>The design journal</option>
				<option>The embo journal</option>
				<option>The febs journal</option>
				<option>The geological society of america</option>
				<option>The historical journal</option>
				<option>The holocene</option>
				<option>The institute of electronics information and communication engineers</option>
				<option>The international journal of psychoanalysis</option>
				<option>The isme journal</option>
				<option>The journal of adhesive dentistry</option>
				<option>The journal of clinical endocrinology and metabolism</option>
				<option>The journal of clinical investigation</option>
				<option>The journal of comparative neurology</option>
				<option>The journal of eukaryotic microbiology</option>
				<option>The journal of hellenic studies</option>
				<option>The journal of immunology</option>
				<option>The journal of juristic papyrology</option>
				<option>The journal of neuropsychiatry and clinical neurosciences</option>
				<option>The journal of neuroscience</option>
				<option>The journal of pain</option>
				<option>The journal of pharmacology and experimental therapeutics</option>
				<option>The journal of physiology</option>
				<option>The journal of the acoustical society of america</option>
				<option>The journal of the torrey botanical society</option>
				<option>The journal of urology</option>
				<option>The journal of wildlife management</option>
				<option>The lancet</option>
				<option>The lichenologist</option>
				<option>The neuroscientist</option>
				<option>The new england journal of medicine</option>
				<option>Theologie und philosophie</option>
				<option>The oncologist</option>
				<option>The open university a251</option>
				<option>The open university harvard</option>
				<option>The open university m801</option>
				<option>The open university numeric</option>
				<option>The open university numeric superscript</option>
				<option>The optical society</option>
				<option>Theory culture and society</option>
				<option>The pharmacogenomics journal</option>
				<option>The plant cell</option>
				<option>The plant journal</option>
				<option>The review of financial studies</option>
				<option>The rockefeller university press</option>
				<option>The scandinavian journal of clinical and laboratory investigation</option>
				<option>The world journal of biological psychiatry</option>
				<option>Thieme e journals vancouver</option>
				<option>Thrombosis and haemostasis</option>
				<option>Tissue engineering</option>
				<option>Toxicon</option>
				<option>Traces</option>
				<option>Traffic</option>
				<option>Traffic injury prevention</option>
				<option>Transactions of the american philological association</option>
				<option>Transportation research record</option>
				<option>Trends journals</option>
				<option>Triangle</option>
				<option>Turabian fullnote bibliography</option>
				<option>Ugeskrift for laeger</option>
				<option>Unified style linguistics</option>
				<option>United nations conference on trade and development</option>
				<option>Universidad evangelica del paraguay</option>
				<option>Universita cattolica del sacro cuore</option>
				<option>Universita di bologna lettere</option>
				<option>Universitat freiburg geschichte</option>
				<option>Universitat heidelberg historisches seminar</option>
				<option>Universite de liege histoire</option>
				<option>Universite de picardie jules verne ufr de medecine</option>
				<option>Universite de sherbrooke faculte d education</option>
				<option>Universite du quebec a montreal</option>
				<option>Universiteit utrecht onderzoeksgids geschiedenis</option>
				<option>Universite laval departement dinformation et de communication</option>
				<option>Universite laval faculte de theologie et de sciences religieuses</option>
				<option>University college dublin school of history and archives</option>
				<option>University of south australia harvard 2011</option>
				<option>University of south australia harvard 2013</option>
				<option>Urban habitats</option>
				<option>Urban studies</option>
				<option>User modeling and user adapted interaction</option>
				<option>Us geological survey</option>
				<option>Vancouver</option>
				<option>Vancouver author date</option>
				<option>Vancouver brackets</option>
				<option>Vancouver brackets no et al</option>
				<option>Vancouver brackets only year no issue</option>
				<option>Vancouver superscript</option>
				<option>Vancouver superscript brackets only year</option>
				<option>Vancouver superscript only year</option>
				<option>Veterinary medicine austria</option>
				<option>Veterinary radiology and ultrasound</option>
				<option>Vienna legal</option>
				<option>Vingtieme siecle</option>
				<option>Virology</option>
				<option>Vision research</option>
				<option>Water environment research</option>
				<option>Water research</option>
				<option>Water science and technology</option>
				<option>Weed science society of america</option>
				<option>Wheaton college phd in biblical and theological studies</option>
				<option>Who europe harvard</option>
				<option>Who europe numeric</option>
				<option>Wissenschaftlicher industrielogistik dialog</option>
				<option>World congress on engineering asset management</option>
				<option>Xenotransplantation</option>
				<option>Yeast</option>
				<option>Zdravniski vestnik</option>
				<option>Zeitschrift fur medienwissenschaft</option>
				<option>Zeitschrift fur soziologie</option>
				<option>Zookeys</option>
				<option>Zootaxa</option>
			</select>

				<div id="formattedRef"></div>
				<script src="/lib/js/chosen.jquery.min.js"></script>
				<script>
				var server = "http://192.168.83.187:5000";

				var authors = $(".formatAuthor").text();
				var r =
				{
					"year": "{pubyear}",
					"authors": JSON.parse("[" + authors + "]"),
					"title": "{_GetArticleTitleForCitation(article_title)}",
					"doi": "10.3897/BDJ.1.e{article_id}",
					"journal": "Biodiversity Data Journal",
					"volume": "1",
					"spage": "e{article_id}"
				}
				var ref   = encodeURIComponent(JSON.stringify(r));
				$(".chosen-select").chosen();
				</script>
			</div>
			</div>
	',

	// Contents

	'article.contents_list_head' => '
		<div class="AOF-Content-holder">
			<ul id="AOF-articleMenu">
	',
	'article.contents_list_foot' => '
			</ul>
		</div>
	',
	'article.contents_list_start' => '

	',
	'article.contents_list_end' => '

	',
	'article.contents_list_nodata' => '

	',
	'article.contents_list_row' => '
			<li id="i{instance_id}" >
				<div class="1" onclick="ScrollArticleToInstance({instance_id});return false;">{object_name}</div>
			</li>
	',

	'article.contents_list_row0' => '
			<li id="i{instance_id}" >
				<div class="2" onclick="ScrollArticleToInstance({instance_id});return false;">{display_name}</div>
			</li>
	',
	'article.contents_list_row1' => '
			<li id="i{instance_id}" >
				<div class="3" onclick="ScrollArticleToInstance({instance_id});return false;">{display_name}</div>
				<ul class="">
					{&}
				</ul>
			</li>
	',

	// Localities
	'article.localities_list_head' => '
		<div class="P-Article-Structures">
			<div class="P-Localities-Map">
				<div class="P-Localities-Map-Inner" id="localitiesMap"></div>
			</div>
			<script>LoadMapScript()</script>
			<div class="P-Localities-Menu">
				<div class="P-Localities-Menu-Row">
					<input type="checkbox" name="active-localities" id="all" value="-2" /><label for="all">All</label>

					<div class="P-Localities-Menu-Row-Clear1">
						<span class="P-Clear-Localities"> ' . getstr('pjs.articleLocalitiesClear') . '</span>
					</div>
				</div>
	',
	'article.localities_list_foot' => '
				<div class="P-Localities-Menu-Row-Clear">
					<span class="P-Clear-Localities"> ' . getstr('pjs.articleLocalitiesClear') . '</span>
				</div>
			</div>
		</div>
		<script>PlaceLocalitiesMenuEvents();</script>

	',
	'article.localities_list_start' => '
				<div class="P-Localities-Menu-Row">
					<input type="checkbox" name="active-localities" id="alltaxa" value="-1"/><label for="alltaxa"> ' . getstr('pjs.articleLocalitiesAllTaxa') . '</label>
				</div>
	',
	'article.localities_list_end' => '

	',
	'article.localities_list_nodata' => '

	',
	'article.localities_list_row' => '
				<div class="P-Localities-Menu-Row-taxa">
					<input type="checkbox" name="active-localities" value="{id}" id="xy{id}"/><label for="xy{id}"> {display_name}</label>
				</div>
	',

	'article.localities_nolocalities' => '
		<div class="P-Article-Structures">

			<div class="P-Localities-Menu">
				<div class="P-Localities-Menu-Row">
					<span class="P-Clear-Localities"> ' . getstr('pjs.articleNoLocalities') . '</span>
				</div>
			</div>
		</div>
	',

	//Taxa list
	'article.taxa_list_head' => '
	',
	'article.taxa_list_foot' => '
	',
	'article.taxa_list_start' => '
	',
	'article.taxa_list_end' => '
	',
	'article.taxa_list_nodata' => 'No taxa
	',
	'article.taxa_list_row' => '
				<div class="taxalistAOF" tnu="INL" {_placeTaxonNamesAttributes(text)} data-taxon-names-count="{_count(text)}">{html} {_showTaxaNameUsage(usage, treatment_id)} {_placeTaxonNavigationLinks(occurrences)}</div>
	',

	// Taxon previews
	// NCBI
	'article.ncbi_lineage_head' => '',
	'article.ncbi_lineage_foot' => '',
	'article.ncbi_lineage_start' => '
			<div class="ncbiDetail">
				<div class="label">Lineage:</div><br/>
	',
	'article.ncbi_lineage_end' => '
			</div>
	',
	'article.ncbi_lineage_row' => '
				<span class="ncbiLineageRow"><a target="_blank" href="{_ParseTaxonExternalLink(taxon_name, link)}">{scientific_name}</a></span>
	',
	'article.ncbi_lineage_nodata' => '',

	'article.ncbi_related_links_head' => '',
	'article.ncbi_related_links_foot' => '',
	'article.ncbi_related_links_start' => '
				<div class="extLinksHolder">
					<div class="extLinksTitle">Related links found in database</div>
	',
	'article.ncbi_related_links_end' => '
					<div class="extLinksSeeAll">To get a complete list click <a target="_blank" href="{_ParseTaxonExternalLink(taxon_name, see_all_link)}">here</a>.</div>
				</div>
	',
	'article.ncbi_related_links_row' => '
					<div class="extLinkRow">
						<a target="_blank" href="{_ParseTaxonExternalLink(taxon_name, link)}" title="{title}">{_CutText(title, 100)}</a>
					</div>
	',
	'article.ncbi_related_links_nodata' => '',
	//Entrez records
	'article.ncbi_entrez_records_title_head' => '',
	'article.ncbi_entrez_records_title_foot' => '',
	'article.ncbi_entrez_records_title_start' => '
				<th>Database name</th>
	',
	'article.ncbi_entrez_records_title_end' => '
	',
	'article.ncbi_entrez_records_title_row' => '
				<th class="entrezDbName">{db_display_name}</th>
	',
	'article.ncbi_entrez_records_title_nodata' => '',

	'article.ncbi_entrez_records_head' => '',
	'article.ncbi_entrez_records_foot' => '',
	'article.ncbi_entrez_records_start' => '
		<div class="entrezRecordsHolder">
			<table class="entrezRecordsTable">
				<tr>
					{title}
				</tr>
				<tr>
					<td>Subtree links</td>
	',
	'article.ncbi_entrez_records_end' => '
				</tr>
			</table>
		</div>
	',
	'article.ncbi_entrez_records_row' => '
				<td class="entrezSubtreeLink">{_ShowEntrezRecordsDbSubtreeLink(taxon_name, taxon_ncbi_id, db_name, records)}</td>
	',
	'article.ncbi_entrez_records_nodata' => '',

	'article.ncbi_no_data' => 'No data on this taxon in NCBI',

	'article.ncbi' => '
		<div class="contentSection imagesSection">
			<table class="contentSectionLabel" cellspacing="0" cellpadding="0" border="0">
				<tr>
					<td class="labelImg" id="ncbiLink">
						<a target="_blank" href="{_ParseTaxonExternalLink(taxon_name, ncbi_link)}"><img class="noBorder" src="' . PTP_URL . '/img/ext_details/ncbi_logo.jpg"></a>
					</td>
					<td><h2 class="labelTitle">Gene Sequences</h2></td>
				</tr>
			</table>
			<div class="sectionBody">
				<div class="ncbiEntrezRecords">
					{entrez_records}
				</div>
				<div class="P-Clear"></div>
			</div>
		</div>
	',
	// GBIF
	'article.gbif' => '
		<div class="contentSection generalInfoSection">
			<table class="contentSectionLabel" cellspacing="0" cellpadding="0" border="0">
				<tr>
					<td class="labelImg"> <a target="_blank" href="{_ParseTaxonExternalLink(taxon_name, gbif_link, postform, postfields)}"><img class="noBorder" src="' . PTP_URL . '/img/ext_details/gbif_logo.jpg"></img></a></td>
					<td><h2 class="labelTitle">Global Biodiversity Information Facility</h2></td>
				</tr>
			</table>
			<div class="sectionBody">
				<script type="text/javascript">
					 function resizeGbifMap(){
						 var iframe = document.getElementById("gbifIframe");
						 var iframeDocument = iframe.contentDocument || iframe.contentWindow.document;
						 var mapi;
						 if (iframeDocument) {
						  mapi = iframeDocument.getElementById("map");
						  mapi.style.width="424px";
						  mapi.style.height="236px";
						 }
					 }
				</script>
				<iframe id="gbifIframe" name="gbifIframe" scrolling="no" height="410" frameborder="0" vspace="1" hspace="1" src="' . IFRAME_PROXY_URL . '?url={_rawurlencode(map_iframe_src)}"  onload="resizeGbifMap(); correctIframeLinks(\'gbifIframe\', \'{link_prefix}\')"></iframe>
			</div>
		</div>
	',

	'article.gbif_no_data' => 'No data on this taxon in GBIF',
	// BHL
	'article.bhl_head' => '
		<div class="contentSection generalInfoSection">
			<table class="contentSectionLabel" cellspacing="0" cellpadding="4" border="0">
				<tr>
					<td class="labelImg" id="biodevLink">
						<a target="_blank" href="{_ParseTaxonExternalLink(taxon_name, bhl_link)}"><img class="noBorder" src="' . PTP_URL . '/img/ext_details/biodev_logo.jpg"></a>
					</td>
					<td><h2 class="labelTitle">Biodiversity Heritage Library</h2></td>
				</tr>
			</table>
			<div class="sectionBody">
				<p>{_bhl_showimage(taxon_name, fullsize_img_url, thumbnail_url, nodata)}
	',

	'article.bhl_foot' => '
			<div class="P-Clear"></div>
			<p class="extLinksSeeAll">To get a complete list click <a target="_blank" href="{_ParseTaxonExternalLink(taxon_name, bhl_link)}">here</a></p>
			</div>
		</div>
	',

	'article.bhl_titles_head' => '
	',

	'article.bhl_titles_foot' => '
	',

	'article.bhl_titles_start' => '
	',

	'article.bhl_titles_end' => '
	',

	'article.bhl_titles_row' => '
				<div class="BHLDetails"><span class="bhl_title">{title}</span>
				<br>{_displayBHLItems(items, taxon_name)}
				</div>
	',

	'article.bhl_titles_nodata' => '
		<div class="sectionBody">
			<p>
				It seems that this taxon name is not present in any BHL pages.
			</p>
			<br/>
		</div>
	',

	'article.bhl_items_head' => '
	',

	'article.bhl_items_foot' => '
	',

	'article.bhl_items_start' => '
	',

	'article.bhl_items_end' => '
	',

	'article.bhl_items_row' => '
				<span>{_bhl_showvolume(volume)}</span>{_displayBHLPages(pages, taxon_name)}
	',

	'article.bhl_items_nodata' => '
	',

	'article.bhl_pages_head' => '
	',

	'article.bhl_pages_foot' => '
	',

	'article.bhl_pages_start' => '
	',

	'article.bhl_pages_end' => '
	',

	'article.bhl_pages_row' => '
				<span class="bhl_pageslink"><a target="_blank" href="{_ParseTaxonExternalLink(taxon_name, url)}">{number}</a>{_bhl_writecomma(rownum, records)}</span>
	',

	'article.bhl_pages_nodata' => '
	',

	'article.bhl_not_successfully_taken' => '
		{*article.bhl_head}
		<p>It seems that this taxon name is present on a very large number of BHL pages.</p><br>
		{*article.bhl_foot}

	',

	'article.bhl' => '
		{*article.bhl_head}
		{titles}
		{*article.bhl_foot}
	',
	// Wikimedia
	'article.wikimedia_no_data' => 'No images on this taxon in Wikimedia',
	'article.wikimedia' => '
		<div class="contentSection imagesSection">
			<table class="contentSectionLabel" cellspacing="0" cellpadding="0" border="0">
				<tr>
					<td class="labelImg" id="{icon_div_id}">
						<a target="_blank" href="{_ParseTaxonExternalLink(taxon_name, wikimedia_link)}">
							<img class="noBorder" src="' . PTP_URL . '/img/ext_details/wikimedia_logo.jpg">
						</a>
					</td>
					<td><h2 class="labelTitle">Images from Wikimedia</h2></td>
				</tr>
			</table>
			<div class="sectionBody">
				{images}
				<div class="P-Clear"></div>
			</div>
		</div>
	',

	'article.wikimedia_images_head' => '
	',

	'article.wikimedia_images_foot' => '
	',

	'article.wikimedia_images_start' => '
	',

	'article.wikimedia_images_end' => '
	',

	'article.wikimedia_images_row' => '
				<div class="imageRow">
					<a target="_blank" href="{_ParseTaxonExternalLink(taxon_name, src)}"><img src="{src}" class="noBorder" alt="{name}"></img></a>
				</div>

	',

	'article.wikimedia_images_nodata' => 'No images on this taxon in Wikimedia',

	// EOL
	'article.eol_no_data' => 'No images on this taxon in EOL',
	'article.eol' => '
		<div class="contentSection imagesSection">
			<table class="contentSectionLabel" cellspacing="0" cellpadding="0" border="0">
				<tr>
					<td class="labelImg" id="{icon_div_id}">
						<a target="_blank" href="{_ParseTaxonExternalLink(taxon_name, eol_link)}">
							<img class="noBorder" src="' . PTP_URL . '/img/ext_details/eol_logo.jpg">
						</a>
					</td>
					<td><h2 class="labelTitle">Encyclopedia of Life</h2></td>
				</tr>
			</table>
			<div class="sectionBody">
				{images}
				<div class="P-Clear"></div>
			</div>
		</div>
	',
	'article.eol_images_head' => '
	',

	'article.eol_images_foot' => '
	',

	'article.eol_images_start' => '
	',

	'article.eol_images_end' => '
	',

	'article.eol_images_row' => '
				<div class="imageRow">
					<a target="_blank" href="{_ParseTaxonExternalLink(taxon_name, url)}"><img src="{url}" class="noBorder" alt=""></img></a>
				</div>
	',

	'article.eol_images_nodata' => '
	',

	// Categories
	'article.category_special_sites_head' => '
	',

	'article.category_special_sites_foot' => '
	',

	'article.category_special_sites_start' => '
			<div class="P-Category-Special-Sites">
	',

	'article.category_special_sites_end' => '
			</div>
	',

	'article.category_special_sites_row' => '
				{preview}
	',

	'article.category_special_sites_nodata' => '
	',


	// Regular sites
	'article.category_regular_sites_head' => '
	',

	'article.category_regular_sites_foot' => '
	',

	'article.category_regular_sites_start' => '
			<div class="P-Category-Regular-Sites">
	',

	'article.category_regular_sites_end' => '
				<div class="P-Clear"></div>
			</div>
	',

	'article.category_regular_sites_row' => '
				<div class="P-Regular-Site-Info-Holder {_displayRegularSiteHasResultsClass(has_results)} {_displayRegularSiteLastRowClass(rownum, records, items_on_row)}">
					<table cellspacing="0" cellpadding="0" border="0" width="100%" height="100%">
						<td class="leftMenuRowImage">
							<a target="_blank" href="{_ParseTaxonExternalLink(taxon_name, taxon_link, 0, use_post_action, fields_to_post)}">
								{_showImageIfSrcExists(picsrc)}
							</a>
						</td>
						<td class="leftMenuRowLink">
							<a target="_blank" href="{_ParseTaxonExternalLink(taxon_name, taxon_link, 0, use_post_action, fields_to_post)}">
								{display_title}
							</a>
						</td>
					</table>
				</div>

	',

	'article.category_regular_sites_nodata' => '
	',

	'article.category' => '
			<a href="#" id="category_{category_name}"></a>
			<div class="P-Category">
				<div class="P-Category-Title">{display_name}</div>
				{special_sites}
				{regular_sites}
			</div>
	',
	// Categories menu
	'article.categories_menu_head' => '
			<div class="P-Categories-Menu">
	',

	'article.categories_menu_foot' => '
				<div class="P-Clear"></div>
			</div>
	',

	'article.categories_menu_start' => '
	',

	'article.categories_menu_end' => '
	',

	'article.categories_menu_row' => '
				<div class="P-Categories-Menu-Element"><a href="#" onclick="ScrollToTaxonCategory(\'{category_name}\');return false;">{display_name}</a></div>

	',

	'article.categories_menu_nodata' => '
	',
	// Categories list
	'article.categories_list_head' => '
			<div class="P-Categories-List">
	',

	'article.categories_list_foot' => '
			</div>
	',

	'article.categories_list_start' => '
	',

	'article.categories_list_end' => '
	',

	'article.categories_list_row' => '
				{preview}

	',

	'article.categories_list_nodata' => '
	',

	'article.taxon_preview' => '
			<div class="P-Taxon">
				<div class="ptp-menu-holder">
					<div class="P-Taxon-Name">{taxon_name}</div>
					{categories_menu}
				</div>
				{categories_list}
			</div>
	'
);

?>