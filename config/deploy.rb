set :application, "spottest"
set :stages, %w(geolocation googleappslogin)

require 'capistrano/ext/multistage'

#set :scm, :subversion
set :svn_username, 'aulitin'
set :svn_password, 'NhKzKZ'
ssh_options[:keys] = %w(~/.ssh/spottest.thinkglobalschool.id_dsa)