BEGIN{istart=-1;end=-1;lastc=0;lastd=0;preproc=-1;solver="satelite";nom="?";sat="UNKNOWN";tt="10000";arc=0;cnfl=0;BUIP=0;red=0;conflicts=0;ext=0;}
{

if($3~"rotations") {lastd=$7;}
if($2~"Nb") {lastc=$8;}
if($0~"^v") {} else {
if($0~"Exiting...too") preproc=0;
if($0~"^s" )
    sat = $2;
if($0 ~ "Bench" && nom=="?")
    {nom=$2};
if($2 ~ "restart") ext=$4;
if($0 ~ "ReduceDB") red= $5;
if($0 ~ "^c real")  {tt = $5;	}
if($2~"FIABLE") sat=$2;
if($1~"ERROR!") sat="SATISFIABLE"
if($2~"conflicts") conflicts=$4;
}
}
END{
if(sat=="unknown") tt=10000;
printf("%s %s %d \n", nom,sat,tt);}
