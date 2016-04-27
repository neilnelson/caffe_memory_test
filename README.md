 
### Caffe memory leak test procedures 
  
author: Neil Nelson  
date: 04/26/2016  

Use [Caffe install](https://github.com/neilnelson/caffe_memory_test/blob/master/caffe_install.md) to get Caffe ready and the test directory set up.

The [first memory test](https://github.com/neilnelson/caffe_memory_test/blob/master/repeats_test.md) repeats an abbreviated Caffe mnist process 30 times and logs the Linux free command at the beginnng and end. The difference between the beginning and ending memory used is averaged across the 30 Caffe runs to obtain an average memory leak value.

The [second memory test](https://github.com/neilnelson/caffe_memory_test/blob/master/chart_100_per_second.md) logs the Linux free command 100 times a second throughout a single Caffe mnist run. The memory used (is charted and a leak estimate made by subtracting the beginnig from the ending memory used.
