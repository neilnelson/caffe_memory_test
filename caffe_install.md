  
### Caffe install procedure used in testing.
  
author: Neil Nelson  
date: 04/26/2016  

[Download Caffe](https://github.com/BVLC/caffe) using the 'Download ZIP' button.  
 
Unzip caffe-master.zip just downloaded.

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
