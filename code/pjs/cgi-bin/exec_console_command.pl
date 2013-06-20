#!/usr/bin/perl

sub populateQueryFields {#slaga get parametrite v hesh s ime %queryString
	%queryString = ();
	my $tmpStr = $ENV{ "QUERY_STRING" };
	@parts = split( /&/, $tmpStr );
	foreach $part (@parts) {
		( $name, $value ) = split( /=/, $part );
		$queryString{ "$name" } = $value;
	}
}
&populateQueryFields;

my $lCommandCode = $queryString{ "command" };

#Pozvolenite komandi koito she executevame
my %gCommands = (
	1 => {
		'command' => '/usr/local/bin/convert',		
		'contentType' => 'image/jpeg',
	},
	2 => {
		'command' => '/usr/local/bin/identify',		
		'contentType' => 'image/jpeg',
	}
);

#Slagame content-type za da moje da testvame prez browser
print "Content-type:" . $gCommands{$lCommandCode}{'contentType'} . "\r\n\r\n";
#~ print "Content-type:text/html \r\n\r\n";

my $lCommand = $gCommands{$lCommandCode}{'command'};

#Predavame parametrite kato argument poneje moje da ima promenliv broi argumenti
my $lCommandArgsCount = $queryString{'argsCount'};

my $lArgNum;


#shte izpolzvame formata na system na koqto se podava list ot parametri a ne string za da ne escape-vame samite argumenti poneje e vgradeno
#Pyrviqt element na masiva trqbva da e samata komanda a sled nego - argumentite v pravilniq red
my @lCommandArr = ();
push(@lCommandArr, $lCommand);
my $lArgValAll = $lCommand;

#Slagame argumentite v masiva
for($lArgNum = 1; $lArgNum <= $lCommandArgsCount; $lArgNum++){
	my $lArgName = 'arg' . $lArgNum;	
	my $lArgVal = $queryString{ $lArgName };
	#url decode
	$lArgVal =~ s/\%([A-Fa-f0-9]{2})/pack('C', hex($1))/seg;
	$lArgValAll = $lArgValAll . ' ' . $lArgVal;
	push(@lCommandArr, $lArgVal);	
}
#~ print @lCommandArr;
#~ print "\n" . $lArgValAll;

#Izpylnqvame komandata - PREDI BESHE S MASIV, NO SE OBURKVASH ZA PURVIA ELEMENT
#~ system(@lCommandArr) == 0
#~ print $lArgValAll;
system($lArgValAll) == 0
        or die "system @lCommandArr failed: $?"
