#!/usr/bin/perl
use strict;
use DBI;

our $mta = "/usr/sbin/sendmail -t";
our $file_dir = "/var/www/pensoft/pwt_items/messaging/";
our $dbname = "pensoft2";
our $dbuser = "iusrpmt";
our $dbpass = "oofskldkjn4l6s8jsd22";
our $dbhost = "localhost";
our $dbport = "5432";

our $ps = "/bin/ps";
our $prgname = "messaging.pl";

our $errs = 0;
our $pid = $$;
our $dbh;
our $sql0 = "update messaging set opid = ? where senddate < current_timestamp and state = 0 and opid = 0";
our $sql1 = "select id, filename from messaging where senddate < current_timestamp and state = 0 and opid = ?";
our $sql2 = "update messaging set state = 1 where id = ?";
our $sql3 = "select distinct opid from messaging where state = 0 and opid > 0";
our $sql4 = "update messaging set opid = 0 where opid = ? and state = 0";

#~ eval{
	$dbh = DBI->connect("dbi:Pg:dbname=$dbname;host=$dbhost;port=$dbport", $dbuser, $dbpass, { RaiseError => 1, AutoCommit => 0 }) || die "can not connect to database";
#~ };
if(!$@ && $dbh){

	my $sth;
	
	my @pidArr = ();
	$sth = $dbh->prepare($sql3);
	$sth->execute();
	while(my @res_arr = $sth->fetchrow_array()){
		push(@pidArr, $res_arr[0]);
	}
	$sth->finish();
	
	
	for(my $i = 0; $i <= $#pidArr; $i++){
		my $cmd = "$ps axuw | grep $prgname | grep $pidArr[$i]";
		my $res = `$cmd`;
		if($res eq ""){
			$sth = $dbh->prepare($sql4);
			$sth->execute($pidArr[$i]);
			$sth->finish();
		}
	}

	my @resArr = ();
	$sth = $dbh->prepare($sql0);
	eval{
		$sth->execute($pid);
		$sth->finish();
	};
	if($@ || $sth->errstr ne ""){
		$errs ++;
	}
	
	if($errs == 0){
		$sth = $dbh->prepare($sql1);
		eval{
			$sth->execute($pid);
			while(my @res_arr = $sth->fetchrow_array()){
				my %tHash;
				$tHash{id} = $res_arr[0];
				$tHash{fname} = $res_arr[1];
				push(@resArr, \%tHash);
			}
			$sth->finish();
		};
		if($@ || $sth->errstr ne ""){
			$errs ++;
		}
	}
	
	if($errs == 0){
		my $i;
		my $cnt = $#resArr;
		for($i = 0; $i <= $cnt; $i++){
			my $fname = $file_dir .$resArr[$i]->{fname};
			if(-f $fname){
				my $cmd = "$mta < $fname";
				print("\t$cmd\n");
				eval{
					system($cmd);
					$sth = $dbh->prepare($sql2);
					print($resArr[$i]->{id});
					$sth->execute($resArr[$i]->{id});
					$sth->finish();
				};
				if($@ || $sth->errstr ne ""){
					$errs ++;
				}
			}
		}
	}
	
	if($errs == 0){
		$dbh->commit();
	} else {
		$dbh->rollback();
	}
	
	$dbh->disconnect();
}


