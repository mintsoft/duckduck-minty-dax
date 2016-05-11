#!/usr/bin/perl

use LWP::Simple;
use Data::Dump qw(dump);
use JSON::XS;


$json = get('https://api.github.com/repos/duckduckgo/zeroclickinfo-goodies/pulls');
dump($json);