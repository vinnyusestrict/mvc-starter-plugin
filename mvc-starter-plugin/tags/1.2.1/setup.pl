#!/usr/bin/perl

use strict;
use warnings;

use Cwd;
use File::Basename;
use Getopt::Long;

use Data::Dumper;

my $curr_dir = cwd();
my $dir_name = basename($curr_dir); 

my %params;

GetOptions(
    'long-name=s'   => \$params{plugin_long_name},
    'desc=s'        => \$params{plugin_desc},
    'class-name=s'  => \$params{plugin_class}
);


&help()
    if ( ! $params{plugin_long_name} || ! $params{plugin_desc} || ! $params{plugin_class} );


my %tmpl = (
    PLUGIN_NAME    => $params{plugin_long_name},
    PLUGIN_DESC    => $params{plugin_desc},
    PluginClass    => $params{plugin_class},
    '<plugin-dir>' => $dir_name,
);


# Do the substitution in all files
foreach my $key ( keys %tmpl )
{
    next unless (defined ($tmpl{$key}) && $tmpl{$key} );
    
#    print qq{find $curr_dir -name "*.php" -o -name "readme.txt" | xargs perl -pi -e 's/$key/$tmpl{$key}/g'};
    `find $curr_dir -name "*.php" -o -name "readme.txt" | xargs perl -pi -e 's/$key/$tmpl{$key}/g'`;
}

# Rename the base file
rename( $curr_dir . '/plugin-name.php', $curr_dir . '/' . $dir_name . '.php' )
    if  -f $curr_dir . '/plugin-name.php';


print "Done!\n";

sub help
{
    print <<EOF;
    
    Usage: perl $0 --long-name="The Plugin Name" --desc="Full Plugin Description" --class-name="DesiredPHPClassName"
    
    All 3 parameters are required. Use a valid class name or your plugin will throw an error during activation.
        
EOF
    exit();

}



__END__

VARS
    PLUGIN_NAME
    PLUGIN_DESC
    PluginClass
    <plugin-dir>
    
Files
    plugin-name.php
    

