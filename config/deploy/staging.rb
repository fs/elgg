set :rails_env, 'staging'

role :app, "spottest.thinkglobalschool.com"
set :user, "ubuntu"
set :port, 22
set :use_sudo, false
set :app_dir, "/home/ubuntu/elgg/current/mod/googleappslogin/"
set :deploy_to, "/home/ubuntu/elgg/googleappslogin/"
#set :branch, "staging"

namespace :deploy do
  desc "Create asset packages for production"
  
  task :update_code, :roles => [:app] do
    run "svn info http://tgs.unfuddle.com/svn/tgs_elgg-google  -rHEAD --username #{svn_username} --password #{svn_password}"
  end

  task :after_update, :roles => [:app] do
    run "cd #{deploy_to}current && svn up"
  end
  
  task :restart, :roles => [:app] do
  end
  
#  task :symlink, :roles => [:app] do
#    run "ln -s #{deploy_to}current #{app_dir}"
#  end
end