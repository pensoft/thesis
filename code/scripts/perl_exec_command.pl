#!/usr/bin/perl

#~ &populateQueryFields;
my $lCommandCode = 1;#$queryString{ "command" };

my %gCommands = (
	1 => {
		'command' => 'ls',
		'argsCount' => 3,
	},
	2 => {
		'command' => 'cd',
		'argsCount' => 1,
	}
);

my $lCommand = $gCommands{$lCommandCode}{'command'};
my $lCommandArgsCount = $gCommands{$lCommandCode}{'argsCount'};

my $lArgNum;
for($lArgNum = 1; $lArgNum <= $lCommandArgsCount; $lArgNum++){
	print ("$lArgNum \n");
}

print $lCommand;