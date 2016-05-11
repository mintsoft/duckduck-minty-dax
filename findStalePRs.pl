#!/usr/bin/perl

use LWP::Simple;
use LWP::UserAgent;
use Data::Dump qw(dump);
use JSON::XS;
use List::Util qw(first max maxstr min minstr reduce shuffle sum any);
use File::Slurp qw(read_file write_file);

my $meta = decode_json(read_file('meta/repo_all.json'));

my ($token) = @ARGV;

my $browser = LWP::UserAgent->new;
$browser->default_header("Authorization" => "token $token");

for my $page (0..1) {
    $json = $browser->get("https://api.github.com/repos/duckduckgo/zeroclickinfo-goodies/issues?labels=Maintainer%20Input%20Requested");
	$issues = decode_json($json->content);
	foreach my $issue(@{$issues}){
		next unless defined $issue->{'pull_request'};
		
        my $comment_response = $browser->get($issue->{'comments_url'});
        my $comments = decode_json($comment_response->content);

        my $pr_desc = $comments->[0]->{'body'};

        my ($maintainer, $ia_page) = get_maintainer($pr_desc);
        my $maintainer_replied = any { lc($_->{'user'}->{'login'}) eq lc($maintainer) } @{$comments};
        print "$issue->{'number'}\t$issue->{'created_at'}\t$maintainer\t$ia_page\t". ($maintainer_replied?"1":"0").$/;
    }
}

sub get_maintainer {
    my($input) = @_;
    my ($ia_page) = $input =~ qr#https://duck.co/ia/view/([a-zA-Z0-9_-]+)#;
    my $ia_meta = $meta->{$ia_page};
    return $meta->{$ia_page}->{'maintainer'}->{'github'}, $ia_page;
}