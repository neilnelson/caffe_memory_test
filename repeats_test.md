  
### Caffe memory leak test procedure - 30 mnist training repeats

#### Repeat an abbreviated mnist training 30 times and measure the memory used at the beginning and the end. 
  
author: Neil Nelson  
date: 04/26/2016  

This procedure is run after Caffe is installed. The installation used here is at [Caffe install procedure used in testing](https://github.com/neilnelson/caffe_memory_test/blob/master/caffe_install.md).

Copy the mem_test.sh from this github location into the test directory, change the permissions.  
```
chmod 744 test/mem_test.sh  
```

and run as follows.  
```
./test/mem_test.sh > test/mem_test.log  
```
The bash file records 'free' at the beginning and end. 30 repeats of the mnist are run using the executable build/tools/caffe. A log of the last training run is in test/run.log.  
  
Since this log will capture memory used by all programs running on the computer, do not initiate or stop any program while the test is running.  
  
After the 30 repeats are done, get the memory used numbers to the right of

```
-/+ buffers/cache:
```

from the two 'free' outputs in test/mem_test.log.  
  
Subtract the beginning number from the ending number. Divide that result by 30 (30 Caffe mnist training runs).  
  
I get (3342636 - 1627552) / 30 = **57169** kilobytes used (leaked) on average by each Caffe mnist training run.  

