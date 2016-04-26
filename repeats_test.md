  
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

mem_test.log
''             total       used       free     shared    buffers     cached
``Mem:      16421400    3231908   13189492      19896     151992    1452364
``-/+ buffers/cache:    1627552   14793848
``Swap:     32767996          0   32767996
``1461465313
``1461465331
``1461465349
``1461465367
``1461465386
``1461465404
``1461465422
``1461465440
``1461465458
``1461465476
``1461465494
``1461465513
``1461465531
``1461465549
``1461465567
``1461465585
``1461465603
``1461465621
``1461465639
``1461465657
``1461465676
``1461465694
``1461465712
``1461465730
``1461465748
``1461465766
``1461465784
``1461465802
``1461465820
``1461465839
``1461465857
``             total       used       free     shared    buffers     cached
``Mem:      16421400    4953212   11468188      20420     153432    1457144
``-/+ buffers/cache:    3342636   13078764
``Swap:     32767996          0   32767996

