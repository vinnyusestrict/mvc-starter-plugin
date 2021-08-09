#!/usr/bin/perl

use strict;
use warnings;

use Cwd;
use File::Basename;
use Getopt::Long;
use Path::Class;

my $curr_dir = cwd();
my $dir_name = basename($curr_dir); 

my %params;

GetOptions(
    'long-name=s'   => \$params{plugin_long_name},
    'desc=s'        => \$params{plugin_desc},
    'class-name=s'  => \$params{plugin_class},
);


&help() unless $params{plugin_long_name} and $params{plugin_desc} and $params{plugin_class};


my %tmpl = (
    '<PLUGIN_NAME>' => $params{plugin_long_name},
    '<PLUGIN_DESC>' => $params{plugin_desc},
    'plugin-slug'   => $dir_name,
    'PluginClass'   => $params{plugin_class}, # Can't use <PluginClass> because the IDE craps out with it.
);


# Do the substitution in all files
foreach my $key ( keys %tmpl )
{
    next unless defined $tmpl{$key} and $tmpl{$key};
    
#    print qq{find $curr_dir -name "*.php" -o -name "readme.txt" | xargs perl -pi -e 's/$key/$tmpl{$key}/g'};
    `find $curr_dir -name "*.php" -o -name "readme.txt" | xargs perl -pi -e 's/$key/$tmpl{$key}/g'`;
}

# Rename the base plugin file
my $curr_file = file( $curr_dir, 'plugin-slug.php' )->stringify;
my $new_file  = file( $curr_dir, "$dir_name.php" )->stringify;
rename( $curr_file, $new_file ) if  -f $curr_file;

# Rename the class files
my $pluginclass = lc( $params{plugin_class} );
for my $filename (`find $curr_dir -name "class-pluginclass-*.php"`)
{
    chomp($filename);
    my $to_file = $filename;
    $to_file =~ s/class-pluginclass/class-$pluginclass/;
    rename( $filename, $to_file );
}


# Rename the boilerplate child file
$curr_file = file( $curr_dir, 't', 'Boilerplate_Child.class.php')->stringify;
$new_file  = file( $curr_dir, 't', $params{plugin_class} . '_Child.class.php')->stringify;
rename( $curr_file, $new_file ) if -f $curr_file;

print "Done! You should now delete me.\n";

sub help
{
    print <<EOF;
    
    Usage: perl $0 --long-name="The Plugin Name" --desc="Full Plugin Description" --class-name="DesiredPHPClassName"
    
    All parameters are required. Use a valid class name or your plugin will throw an error during activation.
    
EOF
    exit();
}



__END__

VARS
    PLUGIN_NAME
    PLUGIN_DESC
    PluginClass
    plugin-slug
    
Files
    plugin-slug.php
    

