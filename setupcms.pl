#!/usr/bin/perl
our $pgpass;
our $sitedbpass;

if($#ARGV < 7 && $#ARGV > 8){
	print { STDERR } "Usage \nperl setupcms.pl <cmstemplate root dir> <destination dir> <database host> <database super user for creating sitedatabase> <site database name> <site database user> <site url> <adm url>\n";
	print { STDERR } "<cmstemplate root dir>: directory where is checkedout the cmstemplate module\n";
	print { STDERR } "<destination dir>: directory where will be placed the real site\n";
	print { STDERR } "<database host>: database host name\n";
	print { STDERR } "<database super user for creating sitedatabase>: database super user which will create site database\n";
	print { STDERR } "<site database name>: database name which will use the site\n";
	print { STDERR } "<site database user>: database user which will use the site\n";
	print { STDERR } "<sitetype>: type of the site you want to create (optional)\n";

	print { STDERR } "<site url>: full url path of the site\n";
	print { STDERR } "<adm url>: full url path of the admin site\n";
	print { STDERR } "example: \nperl setupcms.pl /work/cmstemplate /var/www/cmsetaligent etasrv.etaligent.net postgres cmsetaligent iusretl http://www.etaligent.net/ http://adm.etaligent.net/\n";
	
	exit 1;
} else {
	our $cmstemplateroot=$ARGV[0];
	our $dstdir=$ARGV[1];
	our $sitedbhost=$ARGV[2];
	our $postgresuser=$ARGV[3];
	our $sitedbname=$ARGV[4];
	our $sitedbuser=$ARGV[5];
	our $siteurl=$ARGV[6];
	our $admurl=$ARGV[7];
	#~ nezadyljitelen parametyr za tipa na saiita ako go nqma si syzdava obiknoven cmstemplate
	#~ 1 -> saiit magazin
	our $sitetype=$ARGV[8];
	my $fpdstdir;
	checkAndPrepare($cmstemplateroot , $dstdir , $sitedbhost , $postgresuser , $sitedbname , $sitedbuser);
	$fpdstdir=`cd $dstdir; pwd`;
	chomp($fpdstdir);
	$fpdstdir="$fpdstdir/";
	die "Error: Can't copy contest of $cmstemplateroot to $fpdstdir!" if system("cp -R $cmstemplateroot/* $fpdstdir");
	die "Error: Can't delete dir $fpdstdir/ecmsframew!\n" if system("rm -Rf $fpdstdir/ecmsframew");
	my @dirstodelete=`find $fpdstdir -name CVS`;
	for my $dirtodelete (@dirstodelete) {
		chomp($dirtodelete);
		system("rm -Rf $dirtodelete");
	}
	@dirstodelete=`find $fpdstdir  -type f -name "*.sql"`;
	for my $dirtodelete (@dirstodelete) {
		chomp($dirtodelete);
		replaceinfile("$dirtodelete",("iusrpm", $sitedbuser));
	}
	
	for my $conffile ("$fpdstdir/code/adm/lib/conf.php","$fpdstdir/code/www/lib/conf.php") {
		replaceinfile($conffile,("'PGDB_SRV', 'localhost'", "'PGDB_SRV', '$sitedbhost'",
				"'PGDB_DB', 'cmstemplate'","'PGDB_DB', '$sitedbname'",
				"'PGDB_USR', 'iusrpm'","'PGDB_USR', '$sitedbuser'",
				"'PGDB_PASS', 'hmzsd1jas5kvas'","'PGDB_PASS', '$sitedbpass'",
				"/var/www/cmstemplate/",$fpdstdir,
				"'SITE_URL', 'http://rado.www.cmstempl.etaligent.net/'", "'SITE_URL', '$siteurl'",
				"'ADM_URL', 'http://rado.adm.cmstempl.etaligent.net/'",  "'ADM_URL', '$admurl'"
		));
	}
	

	execsqlfile($fpdstdir."code/sql/tsearch2_bg/uninstall_tsearch2.sql", $sitedbhost , $postgresuser , $sitedbname ,0);
	execsqlfile($fpdstdir."code/sql/tsearch2_bg/tsearch2.sql", $sitedbhost , $postgresuser , $sitedbname ,1);
	execsqlfile($fpdstdir."code/sql/tsearch2_bg/pg_ts.sql", $sitedbhost , $postgresuser , $sitedbname ,1);
	execsqlfile($fpdstdir."code/sql/tables/database_template.sql", $sitedbhost , $postgresuser , $sitedbname ,1);
	#~ ako ima parametyr za tip saiit execute-vame sql-a za magazina
	if ($sitetype == 1) {
		execsqlfile($fpdstdir."code/sql/tables/shop.sql", $sitedbhost , $postgresuser , $sitedbname ,1);
	}
		
	system("chmod -R a+r $fpdstdir*");
	system("chown -R apache:apache $fpdstdir/items/*");
	
	#~ my @dirstodelete=`find $cmstemplateroot  -type f`;
	#~ for my $dirtodelete (@dirstodelete) {
		#~ chomp($dirtodelete);
		#~ if (($dirtodelete !~ /CVS/) ) {
			#~ if (($dirtodelete =~ /\.(php|sql)$/) ) {
				#~ replaceinfile("$dirtodelete",("iusrcosm","iusrpm"));
			#~ }
		#~ }
	#~ }
	#replaceinfile ("$fpdstdir/code/adm/lib/conf.php",("http\:\/\/rado\.kur\.etaligent\.net\/","http\:\/\/rado\.pm\.etaligent\.net\/"));
	print "Setup completed successfuly!\n";
	exit 0;
}

sub execsqlfile {
	my ($dbfile , $sitedbhost , $postgresuser , $sitedbname , $breakonerror) = @_;
	if ($breakonerror > 0) {
		die "Error: Can't init database $sitedbname on host $sitedbhost with file $dbfile\n"  
		if system("PGPASSWORD=\"$pgpass\" psql -h $sitedbhost -U $postgresuser -f $dbfile $sitedbname");
	} else {
		system("PGPASSWORD=\"$pgpass\" psql -h $sitedbhost -U $postgresuser -f $dbfile $sitedbname");
	}
}
sub replaceinfile {
	my ($fname,@sr) = @_;
	my $line,$oldline, $i, $imatch=0;
	open(IFILE,$fname) or die "Error: Can't open $fname for replace!\n";
	if (!open(OFILE,">$fname.new")) {
		close(IFILE);
		die "Error: Can't open $fname.new for write!\n";
	}
	while (<IFILE>) {
		$line=$_;
		$i=0;
		while($i <= $#sr) {
			$line=~ s/$sr[$i]/$sr[$i+1]/g;
			if ($imatch==0) {
				if ( $line ne $_ ) {$imatch=1;}
			}
			$i+=2;
		}
		print OFILE $line;
	}
	close(IFILE);
	close(OFILE);
	if ($imatch !=0) {rename "$fname.new",$fname;}
	else {unlink  "$fname.new";}
}

sub checkAndPrepare {
	my ($cmstemplateroot , $dstdir , $sitedbhost , $postgresuser , $sitedbname , $sitedbuser) = @_;
	my @databases;
	my $dbf=0;
	die "Error: Can't read directory $cmstemplateroot!\n" unless -R $cmstemplateroot;
	die "Error: Destination directory exist and is file or have no write permissions!\n" if -e $dstdir && (!(-W $dstdir) || !(-d $dstdir));
	if (-e $dstdir) {
		my $ff=`ls $dstdir`;
		die"Error: Destination directory $dstdir is not empty!\n" if $ff; 
	} else {
		die"Error: can't create destination directory $dstdir!\n" unless mkdir $dstdir; 
	}
	die"Error: Can't find psql in path!\n" if system("which psql > /dev/null  2>&1");
	die"Error: Can't find createuser in path!\n" if system("which createuser > /dev/null  2>&1");
	die"Error: Can't find createdb in path!\n" if system("which createdb > /dev/null  2>&1");
	print "Password for $postgresuser: ";
	$pgpass = <STDIN>;
	chomp($pgpass);
	die "Error: This script do not support empty passwords!\n" unless $pgpass;
	die "Error: Can't connect to $sitedbhost as $postgresuser with this password!\n" if system("PGPASSWORD=\"$pgpass\" psql -h $sitedbhost -U $postgresuser -l");
	@databases=`PGPASSWORD=\"$pgpass\" psql -h $sitedbhost -U $postgresuser -l`;
	for my $database (@databases) {
		if ($database =~ /\s*$sitedbname\s*\|.*\|.*/) {
			$dbf=1;
		}
	}
	if ($dbf ==1) {
		@databases=`PGPASSWORD=\"$pgpass\" psql -h $sitedbhost -U $postgresuser $sitedbname -c "\\dt"`;
		if ($#databases!=0) { die "Error: The database $sitedbname exists and contains tables!\n";}
	} else {
		die "Error: Can't create database $sitedbname on host $sitedbhost" if system("PGPASSWORD=\"$pgpass\" createdb -h $sitedbhost -U $postgresuser -E UTF8 $sitedbname\n");
	}
	@databases=`PGPASSWORD=\"$pgpass\" psql -h $sitedbhost -U $postgresuser $sitedbname -c "\\du $sitedbuser"`;
	if (($#databases > 0) && ($databases[$#databases-1] =~ /\((\d+)\s*row/)) {
		if ($1=="0") {
			die "Error: Can't create dbuser $sitedbuser on host $sitedbhost\n" 
			if system("PGPASSWORD=\"$pgpass\" createuser -h $sitedbhost -U $postgresuser -S -D -R  $sitedbuser\n");
			$sitedbpass="1";
			my  $pass2="2", $retries=0;
			while ($sitedbpass != $pass2) {
				if ($retries > 0) { print "Passwords are different!\n";}
				print "Please specify $sitedbuser password: ";
				$sitedbpass = <STDIN>;
				chomp($sitedbpass);
				print "Please confirm $sitedbuser password: ";
				$pass2 = <STDIN>;
				chomp($pass2);
				$retries=$retries+1;
			}
			die "Error: Can't create dbuser $sitedbuser on host $sitedbhost\n"  
			if system("PGPASSWORD=\"$pgpass\" psql -h $sitedbhost -U $postgresuser $sitedbname -c \"alter user $sitedbuser  ENCRYPTED PASSWORD '$sitedbpass'\"");
		} else {
			print "Please specify $sitedbuser password: ";
			$sitedbpass = <STDIN>;
			chomp($sitedbpass);
		}
	} else {
		die "Error: Can't understand existing of dbuser $sitedbuser on host $sitedbhost\n";
	}
#	print $databases[$#databases-1];print "\n";	
}