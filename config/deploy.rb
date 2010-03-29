set :application, "spottest"
set :stages, %w(staging)

require 'capistrano/ext/multistage'

#set :scm, :subversion
set :repository, "http://tgs.unfuddle.com/svn/tgs_elgg-google"
set :svn_username, 'aulitin'
set :svn_password, 'NhKzKZ'
ssh_options[:keys] = %w(~/.ssh/spottest.thinkglobalschool.id_dsa)