  
### Caffe memory leak test procedure - Choke your computer

#### Repeat an abbreviated mnist training until the computer starts going in to heavy swap from lack of memory. 
  
author: Neil Nelson  
date: 04/29/2016  

This procedure is run after Caffe is installed. The installation used here is at [Caffe install procedure used in testing](https://github.com/neilnelson/caffe_memory_test/blob/master/caffe_install.md).

Copy the following PHP programs from [this github location](https://github.com/neilnelson/caffe_memory_test) into the test directory.
```
get_memory_usage2.php
caffe_iterations.php
create_mem_csv2.php
```

get_memory_usage2.php records memory statistics from /proc/meminfo every 10 seconds. It also records the time of each observation, the amount of memory the program uses, and kills caffe_iterations.php when the amount of swap used goes above a default or selected limit. The memory statics log is written to test/mem_info.log.
```
get_memory_usage2.php number_of_observations maximum_swap_kilobytes
```
The run parameters are not required.  
number_of_observations defaults to zero.  
maximum_swap defaults to 500000 kilobytes, just under 0.5 gigabytes.

caffe_iterations.php runs the abbreviated Caffe mnist procedure over and over until the number of selected iterations are reached or until the program is killed by get_memory_usage2.php when the amount of swap goes above the selected limit. A csv file of each Caffe run start time is logged in test/run.log.
```
caffe_iterations.php number_of_iterations
```
number_of_iterations is not required and defaults to zero indicating an unlimited number of iterations.

create_mem_csv2.php is run after the test to gather the memory statistics from test/mem_info.log into a csv file that may be loaded into RStudio for the charts.

Run get_memory_usage2.php and caffe_iterations.php in separate terminal windows in the caffe-master directory\. get_memory_usage2.php must be started first and the charts will look better if it is started at least a minute before caffe_iterations.php.  
```
php test/get_memory_usage2.php  
```
Wait a minute then run
```
php test/caffe_iterations.php  
```
After the above runs complete, when the computer builds up swap and get_memory_usage2.php kills caffe_iterations.php, it will be a good idea to reboot the computer so that the memory will be recovered.

In caffe-master run
```
php get_memory_usage2.php
```
to create the memory-usage csv file.

Run the following in RStudio to obtain the primary memory-usage charts.
```
library(ggplot2)
plots <- list()  # new empty list
p1 = qplot(x=seconds, y=UsedLessBuffersCache, data=meminfo, main = colnames(meminfo)[2], ylab=colnames(meminfo)[2])
plots[[1]] <- p1 
p2 = qplot(x=seconds, y=MemFree, data=meminfo, main = colnames(meminfo)[4], ylab=colnames(meminfo)[4])
plots[[2]] <- p3 
p3 = qplot(x=seconds, y=Buffers, data=meminfo, main = colnames(meminfo)[5], ylab=colnames(meminfo)[5])
plots[[3]] <- p4 
p4 = qplot(x=seconds, y=Cached, data=meminfo, main = colnames(meminfo)[6], ylab=colnames(meminfo)[6])
plots[[4]] <- p5 
p5 = qplot(x=seconds, y=Swap, data=meminfo, main = colnames(meminfo)[17], ylab=colnames(meminfo)[17])
plots[[5]] <- p5
multiplot(plotlist = plots, cols = 2)
```
The key chart is UsedLessBuffersCache showing that the available memory drops to zero. And Swap showing how that is increasingly used as available memory gets to zero.

To see charts for all the memory statistics, load the following libraries into RStudio
```
library(ggplot2)
library(grid)
library(gridExtra)
```
and copy the content of [r_create_all_graphs.txt](https://github.com/neilnelson/caffe_memory_test/blob/master/r_create_all_graphs.txt) to RStudio and run it.

Running out of memory on Linux is an unusual event and it is interesting see what happens to the various memory statistics when that occurs.

