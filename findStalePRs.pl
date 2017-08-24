#!/usr/bin/perl

use LWP::Simple;
use LWP::UserAgent;
use Data::Dump qw(dump);
use JSON::XS;
use List::Util qw(first max maxstr min minstr reduce shuffle sum any);
use File::Slurp qw(read_file write_file);

my ($token) = @ARGV;

my $browser = LWP::UserAgent->new;
$browser->default_header("Authorization" => "token $token");

for my $page (1..1) {
	$json = $browser->get("https://api.github.com/repos/duckduckgo/zeroclickinfo-goodies/issues?page=${page}&per_page=1");
	$issues = decode_json($json->content);
	foreach my $issue(@{$issues}){
		next unless defined $issue->{'pull_request'};

	        my $comment_response = $browser->get($issue->{'comments_url'});
		my $comments = decode_json($comment_response->content);
		my $updated_at = $issue->{'updated_at'};
	        my $pr_desc = $comments->[0]->{'body'};

		my $latest_comment = {
			created_at => '1970-01-01T00:00:00Z',
			updated_at => '1970-01-01T00:00:00Z',
			user => "",
			message => "",
		};
		for my $comment ( @{$comments} ) {
			if($latest_comment->{'created_at'} lt $comment->{'created_at'}) {
				$latest_comment = {
					created_at => $comment->{'created_at'},
					updated_at => $comment->{'updated_at'},
					user => $comment->{'user'}->{'login'},
					message => $comment->{'body'},
				};
			}
		}

        	print "$issue->{'number'}\t$issue->{'created_at'}\t$ia_page\t$latest_comment->{'updated_at'} by $latest_comment->{'user'}\n";
	}
}

