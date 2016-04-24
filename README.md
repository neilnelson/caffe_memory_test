  
### Caffe memory leak test procedure ###  
  
author: Neil Nelson  
date: 04/23/2016  

If you already have Caffe installed and the mnist lmdb files created, you can bypass the Caffe download and compile and go directly to 'Create test directory under caffe-master' below.

[Download Caffe](https://github.com/BVLC/caffe) using the 'Download ZIP' button.  
 
Unzip caffe-master.zip.

```
cd caffe-master  
  
ls -l README.md gives 2102 Apr 20 15:57  

cp Makefile.config.example Makefile.config
```

Make changes to Makefile.config as shown in the following diff with Makefile.config.example.  
```
diff Makefile.config.example Makefile.config  
5c5
< # USE_CUDNN := 1
---
> USE_CUDNN := 1
```

Compile Caffe and run the tests.
```
make -j all  
make -j test  
make runtest &> runtest.log  
```

Check that the tests have passed.
```
tail runtest.log  
...  
[----------] Global test environment tear-down  
[==========] 2003 tests from 269 test cases ran. (604379 ms total)  
[  PASSED  ] 2003 tests.  
```

Download the mnist data and create the lmdb files using the steps under _Prepare Datasets_ at [Training LeNet on MNIST with Caffe](http://caffe.berkeleyvision.org/gathered/examples/mnist.html).

You should have the following lmdb files.
```
ls examples/mnist/*lmdb  
examples/mnist/mnist_test_lmdb:  
data.mdb  lock.mdb  
  
examples/mnist/mnist_train_lmdb:  
data.mdb  lock.mdb  
```

#### Create test directory under caffe-master
```
mkdir test  
```

Copy examples/mnist/lenet_solver.prototxt to the test directory.  
```
cp examples/mnist/lenet_solver.prototxt test/  
```

Make the following changes to test/lenet_solver.prototxt to abbreviate the training process and reduce the time to complete.  
```
diff examples/mnist/lenet_solver.prototxt test/lenet_solver.prototxt
8c8
< test_interval: 500
---
> test_interval: 25
18c18
< display: 100
---
> display: 10
20c20
< max_iter: 10000
---
> max_iter: 200
22,23c22,23
< snapshot: 5000
< snapshot_prefix: "examples/mnist/lenet"
---
> # snapshot: 5000
> # snapshot_prefix: "examples/mnist/lenet"  
```

Copy the mem_test.sh from this github location into the test directory, change the permissions.  
```
chmod 744 test/mem_test.sh  
```

and run as follows.  
```
./test/mem_test.sh > test/mem_test.log  
```
The bash file records 'free' at the beginning and end. 30 repeats of the standard mnist are run using the executable build/tools/caffe. A log of the last training run is in test/run.log.  
  
Since this log will capture memory used by all programs running on the computer, do not initiate or stop any program while the test is running.  
  
After the 30 repeats are done, get the memory used numbers to the right of

```
-/+ buffers/cache:
```

from the two 'free' outputs in test/mem_test.log.  
  
Subtract the beginning number from the ending number. Divide that result by 30 (30 Caffe mnist training runs).  
  
I get (3342636 - 1627552) / 30 = **57169** kilobytes used (leaked) on average by each Caffe mnist training run.  
